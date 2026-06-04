@extends('sendportal::layouts.app')

@section('title', __('Email Templates'))

@section('heading')
    {{ __('Email Templates') }}
@endsection

@section('content')

    @component('sendportal::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-light btn-md btn-flat" href="{{ route('sendportal.templates.import') }}">
                <i class="fa fa-upload mr-1"></i> {{ __('Import JSON') }}
            </a>
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.templates.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Template') }}
            </a>
        @endslot
    @endcomponent

    <style>
        .templates-tabs { background: transparent; border-bottom: 2px solid #e9ecef; margin-bottom: 20px; padding: 0; }
        .templates-tabs .nav-link {
            border: 0;
            color: #6c757d;
            padding: 12px 22px;
            font-weight: 500;
            font-size: 14px;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            background: transparent;
        }
        .templates-tabs .nav-link.active {
            color: #2c6df0;
            border-bottom-color: #2c6df0;
            background: transparent;
        }
        .templates-tabs .nav-link .badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
        }
        .kind-meta {
            font-size: 12px;
            color: #8a94a6;
            margin-bottom: 14px;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 6px;
            border-left: 3px solid #e2e8f0;
        }
        .kind-meta.transactional {
            border-left-color: #2c6df0;
        }
        .kind-meta strong { color: #4a5568; }
    </style>

    {{-- Kind Tabs --}}
    <ul class="nav nav-tabs templates-tabs" id="templateKindTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="campaign-kind-tab" data-toggle="tab" href="#campaign-kind-pane" role="tab">
                <i class="fas fa-bullhorn mr-1"></i> {{ __('Campaign Templates') }}
                <span class="badge badge-secondary ml-1">{{ $templates->total() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="transactional-kind-tab" data-toggle="tab" href="#transactional-kind-pane" role="tab">
                <i class="fas fa-paper-plane mr-1"></i> {{ __('Transactional Templates') }}
                <span class="badge badge-info ml-1">{{ $transactionalTemplates->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Campaign tab --}}
        <div class="tab-pane fade show active" id="campaign-kind-pane" role="tabpanel">
            <div class="kind-meta">
                <strong>{{ __('Campaign templates') }}</strong> —
                {{ __('Used for bulk newsletters, campaigns, and automation steps. Edit with the visual builder.') }}
            </div>

            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-search text-muted mr-2"></i>
                        <input type="text" id="template-search" class="form-control form-control-sm border-0"
                               placeholder="{{ __('Search templates by name...') }}" style="box-shadow: none;">
                        <span class="text-muted small ml-2" id="template-count">{{ $templates->total() }} {{ __('templates') }}</span>
                    </div>
                </div>
            </div>

            @include('sendportal::templates.partials.grid')
        </div>

        {{-- Transactional tab --}}
        <div class="tab-pane fade" id="transactional-kind-pane" role="tabpanel">
            <div class="kind-meta transactional">
                <strong>{{ __('Transactional templates') }}</strong> —
                {{ __('Sent via the API one recipient at a time, identified by a unique') }}
                <code>code</code>
                {{ __('(e.g.,') }} <code>shortlist</code>, <code>offered</code>{{ __(', etc.). Variables like') }}
                @{{ candidate_name }}
                {{ __('are rendered at send time.') }}
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('sendportal.templates.transactional.defaults') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-list mr-1"></i> {{ __('Browse defaults') }}
                </a>
                <a href="{{ route('sendportal.templates.transactional.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> {{ __('New Transactional Template') }}
                </a>
            </div>

            @include('sendportal::templates.partials.grid-transactional')
        </div>
    </div>

@endsection

@push('js')
<script>
    $(function() {
        $('#template-search').on('input', function() {
            var search = $(this).val().toLowerCase().trim();
            var count = 0;
            $('#campaign-kind-pane .template-card').each(function() {
                var name = ($(this).data('name') || '').toString().toLowerCase();
                var visible = search === '' || name.indexOf(search) !== -1;
                $(this).toggle(visible);
                if (visible) count++;
            });
            $('#template-count').text(count + ' {{ __("templates") }}');
        });

        // Remember active tab via hash so deep links work and back-button doesn't reset.
        var hash = window.location.hash;
        if (hash === '#transactional') {
            $('#transactional-kind-tab').tab('show');
        }
        $('#templateKindTabs a').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('id');
            history.replaceState(null, '', target === 'transactional-kind-tab' ? '#transactional' : '#');
        });
    });
</script>
@endpush
