@extends('sendportal::layouts.app')

@section('title', $campaign->name)

@section('heading', $campaign->name)

@section('content')

    @include('sendportal::campaigns.reports.partials.nav')

        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="float-left">
                        {{ $template->name }}
                    </div>
{{--                    <div class="float-right">--}}
{{--                        @if ( ! $template->is_in_use)--}}
{{--                            <form action="{{ route('sendportal.templates.destroy', $template->id) }}" method="POST">--}}
{{--                                @csrf--}}
{{--                                @method('DELETE')--}}
{{--                                <a href="{{ route('sendportal.templates.duplicate', $template->id) }}"--}}
{{--                                   class="btn btn-xs btn-light">{{ __('Dublicate') }}</a>--}}
{{--                                <a href="{{ route('sendportal.templates.edit', $template->id) }}"--}}
{{--                                   class="btn btn-xs btn-light">{{ __('Edit') }}</a>--}}
{{--                                <button type="submit" class="btn btn-xs btn-light">{{ __('Delete') }}</button>--}}
{{--                            </form>--}}
{{--                        @endif--}}
{{--                    </div>--}}
                </div>
                <div class="card-body">
                    @include('sendportal::templates.partials.griditem')
                </div>
            </div>
        </div>


@endsection
