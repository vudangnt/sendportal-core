<?php

namespace Sendportal\Base\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Sendportal\Base\Events\MessageBouncedEvent;
use Sendportal\Base\Events\MessageClickedEvent;
use Sendportal\Base\Events\MessageComplainedEvent;
use Sendportal\Base\Events\MessageDeliveredEvent;
use Sendportal\Base\Events\MessageDispatchEvent;
use Sendportal\Base\Events\MessageFailedEvent;
use Sendportal\Base\Events\MessageOpenedEvent;
use Sendportal\Base\Events\MessageSentEvent;
use Sendportal\Base\Events\SubscriberAddedEvent;
use Sendportal\Base\Events\Webhooks\MailgunWebhookReceived;
use Sendportal\Base\Events\Webhooks\MailjetWebhookReceived;
use Sendportal\Base\Events\Webhooks\PostmarkWebhookReceived;
use Sendportal\Base\Events\Webhooks\SendgridWebhookReceived;
use Sendportal\Base\Events\Webhooks\SesWebhookReceived;
use Sendportal\Base\Events\Webhooks\PostalWebhookReceived;
use Sendportal\Base\Listeners\DispatchTransactionalCallbackListener;
use Sendportal\Base\Listeners\MessageDispatchHandler;
use Sendportal\Base\Listeners\Webhooks\HandleMailgunWebhook;
use Sendportal\Base\Listeners\Webhooks\HandleMailjetWebhook;
use Sendportal\Base\Listeners\Webhooks\HandlePostmarkWebhook;
use Sendportal\Base\Listeners\Webhooks\HandleSendgridWebhook;
use Sendportal\Base\Listeners\Webhooks\HandleSesWebhook;
use Sendportal\Base\Listeners\Webhooks\HandlePostalWebhook;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MailgunWebhookReceived::class => [
            HandleMailgunWebhook::class,
        ],
        MessageDispatchEvent::class => [
            MessageDispatchHandler::class,
        ],
        PostmarkWebhookReceived::class => [
            HandlePostmarkWebhook::class,
        ],
        SendgridWebhookReceived::class => [
            HandleSendgridWebhook::class,
        ],
        SesWebhookReceived::class => [
            HandleSesWebhook::class
        ],
        MailjetWebhookReceived::class => [
            HandleMailjetWebhook::class
        ],
        PostalWebhookReceived::class => [
            HandlePostalWebhook::class
        ],
        SubscriberAddedEvent::class => [
            // ...
        ],
        MessageSentEvent::class => [
            DispatchTransactionalCallbackListener::class . '@handleSent',
        ],
        MessageDeliveredEvent::class => [
            DispatchTransactionalCallbackListener::class . '@handleDelivered',
        ],
        MessageOpenedEvent::class => [
            DispatchTransactionalCallbackListener::class . '@handleOpened',
        ],
        MessageClickedEvent::class => [
            DispatchTransactionalCallbackListener::class . '@handleClicked',
        ],
        MessageBouncedEvent::class => [
            DispatchTransactionalCallbackListener::class . '@handleBounced',
        ],
        MessageComplainedEvent::class => [
            DispatchTransactionalCallbackListener::class . '@handleComplained',
        ],
        MessageFailedEvent::class => [
            DispatchTransactionalCallbackListener::class . '@handleFailed',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
