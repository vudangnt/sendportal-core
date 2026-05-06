@extends('sendportal::layouts.app')

@section('title', __('Transactional Message'))

@section('heading', __('Transactional Message'))

@section('content')

    @component('sendportal::layouts.partials.actions')
        @slot('left')
            <a class="btn btn-light btn-md btn-flat" href="{{ route('sendportal.transactional.index') }}">
                <i class="fa fa-arrow-left mr-1"></i> {{ __('Back to List') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-body">
            <h4>{{ $message->subject }}</h4>

            <table class="table table-borderless">
                <tr>
                    <th width="200">{{ __('Message Hash') }}</th>
                    <td><code>{{ $message->hash }}</code></td>
                </tr>
                <tr>
                    <th>{{ __('From') }}</th>
                    <td>{{ $message->from_name }} &lt;{{ $message->from_email }}&gt;</td>
                </tr>
                <tr>
                    <th>{{ __('Recipient') }}</th>
                    <td>{{ $message->recipient_email }}</td>
                </tr>
                <tr>
                    <th>{{ __('Provider Message ID') }}</th>
                    <td><code>{{ $message->message_id ?? '-' }}</code></td>
                </tr>
                <tr>
                    <th>{{ __('Transactional Source') }}</th>
                    <td><code>{{ $message->source ? $message->source->hash : '-' }}</code></td>
                </tr>
            </table>

            <h5 class="mt-4">{{ __('Tracking Timeline') }}</h5>
            <table class="table">
                <tr>
                    <th width="200">{{ __('Queued') }}</th>
                    <td>{{ $message->queued_at ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Sent') }}</th>
                    <td>{{ $message->sent_at ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Delivered') }}</th>
                    <td>{{ $message->delivered_at ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Opened') }}</th>
                    <td>{{ $message->opened_at ?? '-' }} ({{ $message->open_count }} times)</td>
                </tr>
                <tr>
                    <th>{{ __('Clicked') }}</th>
                    <td>{{ $message->clicked_at ?? '-' }} ({{ $message->click_count }} times)</td>
                </tr>
                <tr>
                    <th>{{ __('Bounced') }}</th>
                    <td>{{ $message->bounced_at ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Complained') }}</th>
                    <td>{{ $message->complained_at ?? '-' }}</td>
                </tr>
            </table>

            @if($message->source && $message->source->request_payload)
                <h5 class="mt-4">{{ __('Request Payload') }}</h5>
                <pre class="bg-light p-3"><code>{{ json_encode($message->source->request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
            @endif
        </div>
    </div>

@endsection
