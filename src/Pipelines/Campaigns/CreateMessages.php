<?php

namespace Sendportal\Base\Pipelines\Campaigns;

use Illuminate\Support\Facades\Log;
use Sendportal\Base\Events\MessageDispatchEvent;
use Sendportal\Base\Models\Campaign;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\Subscriber;
use Sendportal\Base\Models\Tag;
use Sendportal\Base\Segments\SegmentResolver;
use Sendportal\Base\Segments\SegmentRule;

class CreateMessages
{
    /**
     * Stores unique subscribers for this campaign
     *
     * @var array
     */
    protected $sentItems = [];
    protected $locationIds = [];

    /**
     * CreateMessages handler
     *
     * @param Campaign $campaign
     * @param $next
     * @return Campaign
     * @throws \Exception
     */
    public function handle(Campaign $campaign, $next)
    {
        // Chỉ nhánh segment rẽ ra sớm. Mọi thứ phía dưới giữ NGUYÊN XI thứ tự cũ —
        // đặc biệt là vòng nạp locationIds phải chạy TRƯỚC cả send_to_all lẫn handleTags.
        // Đẩy nó xuống nhánh else là đổi hành vi: handleAllSubscribers cũng đi qua
        // canSendToSubscriber, nên campaign send_to_all CÓ chọn location hôm nay vẫn
        // đang bị lọc location; bỏ vòng này là tệp nhận của nó phình ra.
        // (Prod hôm nay có 0 campaign send_to_all, nhưng đừng gài mìn cho campaign sau.)
        if (!$campaign->send_to_all && $campaign->targeting_mode === 'segment') {
            $this->handleSegment($campaign);

            return $next($campaign);
        }

        foreach ($campaign->locations as $location) {
            $this->locationIds[] = $location->id;
        }

        if ($campaign->send_to_all) {
            $this->handleAllSubscribers($campaign);
        } else {
            $this->handleTags($campaign);
        }

        return $next($campaign);
    }

    /**
     * Nhắm theo rule: OR trong dimension, AND giữa dimension.
     *
     * KHÔNG nạp $this->locationIds — ở chế độ này location là tag LOC_ bình thường nằm
     * trong rule, nên khối array_intersect trong canSendToSubscriber tự bỏ qua
     * (nó đã có sẵn guard `if (!empty($this->locationIds))`).
     */
    protected function handleSegment(Campaign $campaign): void
    {
        $rule = SegmentRule::fromTags($campaign->tags);

        if ($rule->isEmpty()) {
            Log::warning('- Campaign segment rỗng, không gửi cho ai. campaign_id=' . $campaign->id);

            return;
        }

        $resolver = app(SegmentResolver::class);

        // PHẢI dùng chunkById, KHÔNG dùng chunk(): chunk() phân trang bằng LIMIT/OFFSET,
        // ai đó huỷ đăng ký giữa đợt gửi là offset trượt và BỎ SÓT người nhận, im lặng.
        // chunkById đúng ở đây vì ts.subscriber_id chính là khoá GROUP BY, nên lọc trước
        // khi gom nhóm tương đương lọc sau. Tham số mặc định KHÔNG chạy được: cả ba bảng
        // join đều có cột `id` -> lỗi 1052 "Column 'id' in order clause is ambiguous".
        $resolver->query($campaign->workspace_id, $rule)
            ->chunkById(1000, function ($rows) use ($campaign) {
                $ids = array_map(static fn ($r) => (int) $r->subscriber_id, $rows->all());
                $subscribers = Subscriber::whereIn('id', $ids)->get();
                $this->dispatchToSubscriber($campaign, $subscribers);
            }, 'ts.subscriber_id', 'subscriber_id');
    }

    /**
     * Handle a campaign where all subscribers have been selected
     *
     * @param Campaign $campaign
     * @throws \Exception
     */
    protected function handleAllSubscribers(Campaign $campaign)
    {
        Subscriber::where('workspace_id', $campaign->workspace_id)
            ->whereNull('unsubscribed_at')
            ->chunkById(500, function ($subscribers) use ($campaign) {
                $this->dispatchToSubscriber($campaign, $subscribers);
            }, 'id');
    }

    /**
     * Loop through each tag
     *
     * @param Campaign $campaign
     */
    protected function handleTags(Campaign $campaign)
    {
        foreach ($campaign->tags as $tag) {
            $this->handleTag($campaign, $tag);
        }
    }

    /**
     * Handle each tag
     *
     * @param Campaign $campaign
     * @param Tag $tag
     *
     * @return void
     */
    protected function handleTag(Campaign $campaign, Tag $tag): void
    {
        \Log::info('- Handling Campaign Tag id=' . $tag->id);

        $tag->subscribers()->whereNull('unsubscribed_at')->chunkById(1000, function ($subscribers) use ($campaign) {
            $this->dispatchToSubscriber($campaign, $subscribers);
        }, 'sendportal_subscribers.id');
    }

    /**
     * Dispatch the campaign to a given subscriber
     *
     * @param Campaign $campaign
     * @param $subscribers
     */
    protected function dispatchToSubscriber(Campaign $campaign, $subscribers)
    {
        \Log::info('- Number of subscribers in this chunk: ' . count($subscribers));

        foreach ($subscribers as $subscriber) {
            if (!$this->canSendToSubscriber($campaign->id, $subscriber->id, $subscriber)) {
                continue;
            }

            $this->dispatch($campaign, $subscriber);
        }
    }

    /**
     * Check if we can send to this subscriber
     * @param int $campaignId
     * @param int $subscriberId
     *
     * @return bool
     * @todo check how this would impact on memory with 200k subscribers?
     *
     */
    protected function canSendToSubscriber($campaignId, $subscriberId, $subscriber): bool
    {
        $key = $campaignId . '-' . $subscriberId;

        $subscriberLocation = $subscriber->locations->pluck('id')->toArray();

        if (!empty($this->locationIds)) {
            $phanTuChung = array_intersect($subscriberLocation, $this->locationIds);
            if (!empty($phanTuChung)) {
                Log::info("Mảng A có các phần tử tồn tại trong mảng B: ",
                    ["subscriberLocation" => $subscriberLocation, "locationid" => $this->locationIds]);
            } else {
                Log::info("Không có phần tử nào từ mảng A nằm trong mảng B. ",
                    ["subscriberLocation" => $subscriberLocation, "locationid" => $this->locationIds]
                );
                return false;
            }
        }

        if (in_array($key, $this->sentItems, true)) {
            Log::info('- Subscriber has already been sent a message campaign_id=' . $campaignId . ' subscriber_id=' . $subscriberId);
            return false;
        }

        $this->appendSentItem($key);

        return true;
    }

    /**
     * Append a value to the sentItems
     *
     * @param string $value
     * @return void
     */
    protected function appendSentItem(string $value): void
    {
        $this->sentItems[] = $value;
    }

    /**
     * Dispatch the message
     *
     * @param Campaign $campaign
     * @param Subscriber $subscriber
     */
    protected function dispatch(Campaign $campaign, Subscriber $subscriber): void
    {
        if ($campaign->save_as_draft) {
            $this->saveAsDraft($campaign, $subscriber);
        } else {
            $this->dispatchNow($campaign, $subscriber);
        }
    }

    /**
     * Dispatch a message now
     *
     * @param Campaign $campaign
     * @param Subscriber $subscriber
     * @return Message
     */
    protected function dispatchNow(Campaign $campaign, Subscriber $subscriber): Message
    {
        // If a message already exists, then we're going to assume that
        // it has already been dispatched. This makes the dispatch fault-tolerant
        // and prevent dispatching the same message to the same subscriber
        // more than once
        if ($message = $this->findMessage($campaign, $subscriber)) {
            \Log::info('Message has previously been created campaign=' . $campaign->id . ' subscriber=' . $subscriber->id);

            return $message;
        }

        // Validate required fields before creating message
        if (empty($subscriber->email)) {
            \Log::error('Cannot create message: subscriber email is empty', [
                'campaign_id' => $campaign->id,
                'subscriber_id' => $subscriber->id,
            ]);
            throw new \Exception('Subscriber email is required');
        }

        if (empty($campaign->subject)) {
            \Log::warning('Campaign subject is empty', [
                'campaign_id' => $campaign->id,
                'subscriber_id' => $subscriber->id,
            ]);
        }

        // the message doesn't exist, so we'll create and dispatch
        \Log::info('Creating email message', [
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
            'recipient_email' => $subscriber->email,
            'subject' => $campaign->subject,
        ]);

        $attributes = [
            'workspace_id' => $campaign->workspace_id,
            'subscriber_id' => $subscriber->id,
            'source_type' => Campaign::class,
            'source_id' => $campaign->id,
            'recipient_email' => $subscriber->email,
            'subject' => $campaign->subject,
            'from_name' => $campaign->from_name,
            'from_email' => $campaign->from_email,
            'reply_to_email' => $campaign->reply_to_email,
            'queued_at' => null,
            'sent_at' => null,
        ];

        try {
        $message = new Message($attributes);
        $message->save();

            \Log::info('Email message created successfully', [
                'message_id' => $message->id,
                'campaign_id' => $campaign->id,
                'subscriber_id' => $subscriber->id,
            ]);

        event(new MessageDispatchEvent($message));

        return $message;
        } catch (\Exception $e) {
            \Log::error('Failed to create email message', [
                'campaign_id' => $campaign->id,
                'subscriber_id' => $subscriber->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * @param Campaign $campaign
     * @param Subscriber $subscriber
     */
    protected function saveAsDraft(Campaign $campaign, Subscriber $subscriber)
    {
        \Log::info('Saving message as draft campaign=' . $campaign->id . ' subscriber=' . $subscriber->id);

        Message::firstOrCreate(
            [
                'workspace_id' => $campaign->workspace_id,
                'subscriber_id' => $subscriber->id,
                'source_type' => Campaign::class,
                'source_id' => $campaign->id,
            ],
            [
                'recipient_email' => $subscriber->email,
                'subject' => $campaign->subject,
                'from_name' => $campaign->from_name,
                'from_email' => $campaign->from_email,
                'reply_to_email' => $campaign->reply_to_email,
                'queued_at' => now(),
                'sent_at' => null,
            ]
        );
    }

    protected function findMessage(Campaign $campaign, Subscriber $subscriber): ?Message
    {
        return Message::where('workspace_id', $campaign->workspace_id)
            ->where('subscriber_id', $subscriber->id)
            ->where('source_type', Campaign::class)
            ->where('source_id', $campaign->id)
            ->first();
    }
}
