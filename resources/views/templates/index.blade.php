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

    {{-- Search --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-search text-muted mr-2"></i>
                <input type="text" id="template-search" class="form-control form-control-sm border-0" placeholder="{{ __('Search templates by name...') }}" style="box-shadow: none;">
                <span class="text-muted small ml-2" id="template-count">{{ $templates->total() }} {{ __('templates') }}</span>
            </div>
        </div>
    </div>

    @include('sendportal::templates.partials.grid')

@endsection

@push('js')
<script>
    $(function() {
        $('#template-search').on('input', function() {
            var search = $(this).val().toLowerCase().trim();
            var count = 0;
            $('.template-card').each(function() {
                var name = $(this).data('name').toLowerCase();
                var visible = search === '' || name.indexOf(search) !== -1;
                $(this).toggle(visible);
                if (visible) count++;
            });
            $('#template-count').text(count + ' {{ __("templates") }}');
        });
    });
</script>
@endpush
