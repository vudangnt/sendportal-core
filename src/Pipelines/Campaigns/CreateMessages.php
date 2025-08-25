<?php

namespace Sendportal\Base\Pipelines\Campaigns;

use Illuminate\Support\Facades\Log;
use Sendportal\Base\Events\MessageDispatchEvent;
use Sendportal\Base\Models\Campaign;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\Subscriber;
use Sendportal\Base\Models\Tag;

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

        // the message doesn't exist, so we'll create and dispatch
        \Log::info('Saving empty email message campaign=' . $campaign->id . ' subscriber=' . $subscriber->id);
        $attributes = [
            'workspace_id' => $campaign->workspace_id,
            'subscriber_id' => $subscriber->id,
            'source_type' => Campaign::class,
            'source_id' => $campaign->id,
            'recipient_email' => $subscriber->email,
            'subject' => $campaign->subject,
            'from_name' => $campaign->from_name,
            'from_email' => $campaign->from_email,
            'queued_at' => null,
            'sent_at' => null,
        ];

        $message = new Message($attributes);
        $message->save();

        event(new MessageDispatchEvent($message));

        return $message;
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
