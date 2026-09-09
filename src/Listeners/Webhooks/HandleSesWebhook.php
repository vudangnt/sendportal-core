<?php

declare(strict_types=1);

namespace Sendportal\Base\Listeners\Webhooks;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Sendportal\Base\Events\Webhooks\SesWebhookReceived;
use Sendportal\Base\Services\Webhooks\EmailWebhookService;

class HandleSesWebhook implements ShouldQueue
{
    /** @var EmailWebhookService */
    private $emailWebhookService;

    public function __construct(EmailWebhookService $emailWebhookService)
    {
        $this->emailWebhookService = $emailWebhookService;
    }

    /**
     * @throws Exception
     */
    public function handle(SesWebhookReceived $event): void
    {
        if ($event->payloadType === 'SubscriptionConfirmation') {
            $subscribeUrl = Arr::get($event->payload, 'SubscribeURL');

            $httpClient = new Client();
            $httpClient->get($subscribeUrl);

            Log::info('subscribing', ['url' => $subscribeUrl]);
            return;
        }

        $event = json_decode(Arr::get($event->payload, 'Message'), true);

        if (!$event) {
            return;
        }

        $this->processEmailEvent($event);
    }

    /**
     * @throws Exception
     */
    private function processEmailEvent(array $event): void
    {
        /** @var string|null $messageId */
        $messageId = $event['mail']['messageId'] ?? null;

        /** @var string|null $eventType */
        $eventType = $event['eventType'] ?? null;

        if (!$eventType || !$messageId) {
            return;
        }
        
        $eventType = strtolower($eventType);
        
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-examples.html#event-publishing-retrieving-sns-open
        // Bounce, Complaint, Message, Send Email, Reject Event, Open Event, Click Event
        switch ($eventType) {
            case 'click':
                $this->handleClick($messageId, $event);
                break;

            case 'open':
                $this->handleOpen($messageId, $event);
                break;

            case 'reject':
                $this->handleReject($messageId, $event);
                break;

            case 'delivery':
                $this->handleDelivery($messageId, $event);
                break;

            case 'complaint':
                $this->handleComplaint($messageId, $event);
                break;

            case 'bounce':
                $this->handleBounce($messageId, $event);
                break;
        }
    }

    /**
     * @throws Exception
     */
    private function handleClick(string $messageId, array $event): void
    {
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-examples.html#event-publishing-retrieving-sns-click
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-contents.html#event-publishing-retrieving-sns-contents-click-object
        $link = Arr::get($event, 'click.link');
        $timestamp = Carbon::parse(Arr::get($event, 'click.timestamp'))->setTimezone('UTC');

        $this->emailWebhookService->handleClick($messageId, $timestamp, $link);
    }

    /**
     * @throws Exception
     */
    private function handleOpen(string $messageId, array $event): void
    {
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-contents.html#event-publishing-retrieving-sns-contents-open-object
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-examples.html#event-publishing-retrieving-sns-open
        $ipAddress = Arr::get($event, 'open.ipAddress');
        $timestamp = Carbon::parse(Arr::get($event, 'open.timestamp'))->setTimezone('UTC');

        $this->emailWebhookService->handleOpen($messageId, $timestamp, $ipAddress);
    }

    private function handleReject(string $messageId, array $event): void
    {
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-contents.html#event-publishing-retrieving-sns-contents-reject-object
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-examples.html#event-publishing-retrieving-sns-reject
        //
        // SES đã nhận thư rồi mới từ chối gửi đi (virus, hoặc địa chỉ nằm trong danh sách
        // chặn của tài khoản). Thư KHÔNG tới nơi. Trước đây hàm này rỗng nên sự kiện bị vứt.
        // Chỉ GHI NHẬN, không huỷ đăng ký — huỷ đăng ký vẫn dành riêng cho bounce permanent.
        $this->emailWebhookService->handleFailure(
            $messageId,
            'Reject',
            (string) Arr::get($event, 'reject.reason', 'SES rejected the message'),
            Carbon::parse(Arr::get($event, 'mail.timestamp') ?: 'now')->setTimezone('UTC')
        );
    }

    private function handleDelivery(string $messageId, array $event): void
    {
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/ses/latest/DeveloperGuide/ses/latest/DeveloperGuide/notification-contents.html.html#delivery-object
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/event-publishing-retrieving-sns-examples.html#event-publishing-retrieving-sns-delivery
        $timestamp = Carbon::parse(Arr::get($event, 'delivery.timestamp'))->setTimezone('UTC');

        $this->emailWebhookService->handleDelivery($messageId, $timestamp);
    }

    private function handleComplaint(string $messageId, array $event): void
    {
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#complaint-object
        // $complaint = \Arr::get($event, 'complaint');
        // $feedbackType = \Arr::get($complaint, 'complaintFeedbackType');

        // abuse — Indicates unsolicited email or some other kind of email abuse.
        // auth-failure — Email authentication failure report.
        // fraud — Indicates some kind of fraud or phishing activity.
        // not-spam — Indicates that the entity providing the report does not consider the message to be spam. This may be used to correct a message that was incorrectly tagged or categorized as spam.
        // other — Indicates any other feedback that does not fit into other registered types.
        // virus — Reports that a virus is found in the originating message.
        //
        // https://aws.amazon.com/blogs/messaging-and-targeting/handling-bounces-and-complaints/

        $timestamp = Carbon::parse(Arr::get($event, 'complaint.timestamp'))->setTimezone('UTC');

        $this->emailWebhookService->handleComplaint($messageId, $timestamp);
    }

    private function handleBounce(string $messageId, array $event): void
    {
        // https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#bounce-object
        $bounceType = Arr::get($event, 'bounce.bounceType');
        $timestamp = Carbon::parse(Arr::get($event, 'bounce.timestamp'))->setTimezone('UTC');

        // https://aws.amazon.com/blogs/messaging-and-targeting/handling-bounces-and-complaints/
        if (strtolower($bounceType) === 'permanent') {
            $this->emailWebhookService->handlePermanentBounce($messageId, $timestamp);

            return;
        }

        // AWS trả 3 loại bounce: permanent (hộp thư không tồn tại), transient (hộp đầy,
        // thư quá lớn, bị từ chối tạm thời) và undetermined. Trước đây CHỈ permanent được
        // ghi, hai loại kia nhận xong vứt luôn — nên con số bounce trong app thấp hơn thật
        // nhiều lần (04/09/2026: AWS đếm 611, hai DB cộng lại chỉ có 238), và bảng
        // sendportal_message_failures rỗng trong khi MỌI adapter ESP khác đều ghi vào đó.
        //
        // Chỉ GHI NHẬN. Không đổi một quyết định gửi nào: handleFailure() chỉ tạo bản ghi
        // MessageFailure và bắn event, KHÔNG huỷ đăng ký — huỷ đăng ký vẫn chỉ dành cho
        // bounce permanent như cũ.
        $moTa = array_filter([
            Arr::get($event, 'bounce.bounceSubType'),
            Arr::get($event, 'bounce.bouncedRecipients.0.diagnosticCode'),
        ]);

        $this->emailWebhookService->handleFailure(
            $messageId,
            (string) $bounceType,
            $moTa ? implode(' — ', $moTa) : 'Bounce ' . $bounceType,
            $timestamp
        );
    }
}
