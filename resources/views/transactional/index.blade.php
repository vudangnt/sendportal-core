@extends('sendportal::layouts.app')

@section('title', __('Transactional Messages'))

@section('heading', __('Transactional Messages'))

@section('content')

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Total') }}</h5>
                    <h2>{{ number_format($stats['total']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Delivered') }}</h5>
                    <h2>{{ number_format($stats['delivered']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Opened') }}</h5>
                    <h2>{{ number_format($stats['opened']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Clicked') }}</h5>
                    <h2>{{ number_format($stats['clicked']) }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    @component('sendportal::layouts.partials.actions')
        @slot('left')
            <form action="{{ route('sendportal.transactional.index') }}" method="GET" class="form-inline">
                <div class="mr-2">
                    <input type="text" class="form-control" placeholder="{{ __('Search...') }}" name="search" value="{{ request('search') }}">
                </div>

                <div class="mr-2">
                    <select name="status" class="form-control">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="queued" {{ request('status') == 'queued' ? 'selected' : '' }}>{{ __('Queued') }}</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ __('Sent') }}</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="opened" {{ request('status') == 'opened' ? 'selected' : '' }}>{{ __('Opened') }}</option>
                        <option value="clicked" {{ request('status') == 'clicked' ? 'selected' : '' }}>{{ __('Clicked') }}</option>
                        <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>{{ __('Bounced') }}</option>
                        <option value="complained" {{ request('status') == 'complained' ? 'selected' : '' }}>{{ __('Complained') }}</option>
                    </select>
                </div>

                <div class="mr-2">
                    <select name="sender_domain" class="form-control">
                        <option value="">{{ __('All Sender Domains') }}</option>
                        @foreach($senderDomains as $domain)
                            <option value="{{ $domain }}" {{ request('sender_domain') == $domain ? 'selected' : '' }}>{{ $domain }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mr-2">
                    <select name="recipient_domain" class="form-control">
                        <option value="">{{ __('All Recipient Domains') }}</option>
                        @foreach($recipientDomains as $domain)
                            <option value="{{ $domain }}" {{ request('recipient_domain') == $domain ? 'selected' : '' }}>{{ $domain }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-light">{{ __('Filter') }}</button>

                @if(request()->anyFilled(['search', 'status', 'sender_domain', 'recipient_domain', 'from', 'to']))
                    <a href="{{ route('sendportal.transactional.index') }}" class="btn btn-light ml-2">{{ __('Clear') }}</a>
                @endif
            </form>
        @endslot

        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.messages.index') }}">
                <i class="fa fa-envelope mr-1"></i> {{ __('Campaign Messages') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Sent At') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Recipient') }}</th>
                        <th>{{ __('From') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Opens') }}</th>
                        <th>{{ __('Clicks') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr>
                            <td>
                                <a href="{{ route('sendportal.transactional.show', $message->id) }}">
                                    {{ $message->sent_at ? $message->sent_at->format('Y-m-d H:i:s') : ($message->queued_at ? $message->queued_at->format('Y-m-d H:i:s') : '-') }}
                                </a>
                            </td>
                            <td>{{ $message->subject }}</td>
                            <td>{{ $message->recipient_email }}</td>
                            <td>{{ $message->from_email }}</td>
                            <td>
                                @if($message->bounced_at)
                                    <span class="badge badge-danger">{{ __('Bounced') }}</span>
                                @elseif($message->complained_at)
                                    <span class="badge badge-warning">{{ __('Complained') }}</span>
                                @elseif($message->clicked_at)
                                    <span class="badge badge-success">{{ __('Clicked') }}</span>
                                @elseif($message->opened_at)
                                    <span class="badge badge-info">{{ __('Opened') }}</span>
                                @elseif($message->delivered_at)
                                    <span class="badge badge-primary">{{ __('Delivered') }}</span>
                                @elseif($message->sent_at)
                                    <span class="badge badge-secondary">{{ __('Sent') }}</span>
                                @else
                                    <span class="badge badge-light">{{ __('Queued') }}</span>
                                @endif
                            </td>
                            <td>{{ $message->open_count }}</td>
                            <td>{{ $message->click_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <p class="empty-table-text">{{ __('No transactional messages found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($messages->total() > 0)
        <div class="row">
            <div class="col-md-12">
                {{ $messages->appends(request()->query())->links() }}
            </div>
        </div>
    @endif

@endsection
