@extends('sendportal::layouts.app')

@section('title', __('Campaigns'))

@section('heading')
    {{ __('Campaigns') }}
@endsection

@section('content')

    @include('sendportal::campaigns.partials.nav')

    @component('sendportal::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.campaigns.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Campaign') }}
            </a>
        @endslot
    @endcomponent

    <style>
        .max-width-200 {
            max-width: 200px;
            width: auto;       /* Let it adjust based on content */
            word-wrap: break-word; /* Ensure long words break to fit within the width */
        }

        .text-container {
            overflow: hidden;
            position: relative;
        }

        .tags-content {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap; /* Prevent text from wrapping */
        }

        .locations-content {
            display: block; /* Always show Locations content */
        }

        .text-container.expanded .tags-content {
            white-space: normal; /* Allow full text to show when expanded */
        }
        .text-toggle-button:hover {
            text-decoration: underline; /* Adds underline effect on hover */
            cursor: pointer; /* Shows pointer cursor on hover */
        }
        .text-container.expanded .text-toggle-button {
            display: none; /* Hide the button when expanded */
        }

    </style>
    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    @if (request()->routeIs('sendportal.campaigns.sent'))
                        <th>{{ __('Target Audience') }}</th>
                        <th>{{ __('Sent') }}</th>
                        <th>{{ __('Opened') }}</th>
                        <th>{{ __('Clicked') }}</th>
                    @endif
                    <th>{{ __('Created') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            @if ($campaign->draft)
                                <a href="{{ route('sendportal.campaigns.edit', $campaign->id) }}">{{ $campaign->name }}</a>
                            @elseif($campaign->sent)
                                <a href="{{ route('sendportal.campaigns.reports.index', $campaign->id) }}">{{ $campaign->name }}</a>
                            @else
                                <a href="{{ route('sendportal.campaigns.status', $campaign->id) }}">{{ $campaign->name }}</a>
                            @endif
                        </td>
                        @if (request()->routeIs('sendportal.campaigns.sent'))
                            <td class="max-width-200">
                                <div class="text-container">
                                    <span class="locations-content">
                                        Locations: {{ implode(', ', $campaign->locations->pluck('name')->toArray()) }}
                                    </span>
                                    <span class="tags-content">
                                        Tags: {{ implode(', ', $campaign->tags->pluck('name')->toArray()) }}<br/>
                                    </span>
                                </div>
                                <span class="btn-link text-toggle-button">
                                    Xem thêm
                                </span>
                            </td>
                            <td>{{ $campaignStats[$campaign->id]['counts']['sent'] }}</td>
                            <td>{{ number_format($campaignStats[$campaign->id]['ratios']['open'] * 100, 1) . '%' }}</td>
                            <td>
                                {{ number_format($campaignStats[$campaign->id]['ratios']['click'] * 100, 1) . '%' }}
                            </td>
                        @endif
                        <td><span
                                title="{{ $campaign->created_at }}">{{ $campaign->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            @include('sendportal::campaigns.partials.status')
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm btn-wide" type="button" id="dropdownMenuButton"
                                        data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true"
                                        aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($campaign->draft)
                                        <a href="{{ route('sendportal.campaigns.edit', $campaign->id) }}"
                                           class="dropdown-item">
                                            {{ __('Edit') }}
                                        </a>
                                    @else
                                        <a href="{{ route('sendportal.campaigns.reports.index', $campaign->id) }}"
                                           class="dropdown-item">
                                            {{ __('View Report') }}
                                        </a>
                                    @endif

                                    <a href="{{ route('sendportal.campaigns.duplicate', $campaign->id) }}"
                                       class="dropdown-item">
                                        {{ __('Duplicate') }}
                                    </a>

                                    @if($campaign->canBeCancelled())
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('sendportal.campaigns.confirm-cancel', $campaign->id) }}"
                                           class="dropdown-item">
                                            {{ __('Cancel') }}
                                        </a>
                                    @endif

                                    <div class="dropdown-divider"></div>
                                    <a href="{{ route('sendportal.campaigns.destroy.confirm', $campaign->id) }}"
                                       class="dropdown-item text-danger">
                                        <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            <p class="empty-table-text">
                                @if (request()->routeIs('sendportal.campaigns.index'))
                                    {{ __('You do not have any draft campaigns.') }}
                                @else
                                    {{ __('You do not have any sent campaigns.') }}
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('sendportal::layouts.partials.pagination', ['records' => $campaigns])
    <script>
        document.querySelectorAll('.text-toggle-button').forEach(button => {
            button.addEventListener('click', function () {
                const container = this.closest('td').querySelector('.text-container');
                container.classList.toggle('expanded');

                if (container.classList.contains('expanded')) {
                    this.textContent = 'Thu nhỏ'; // Change button text to "Show Less"
                } else {
                    this.textContent = 'Xem thêm'; // Change back to "Show More"
                }
            });
        });

    </script>
@endsection
