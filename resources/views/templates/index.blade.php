@extends('sendportal::layouts.app')

@section('title', __('Email Templates'))

@section('heading')
    {{ __('Email Templates') }}
@endsection

@section('content')

    @component('sendportal::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.templates.import') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('Import Template by json') }}
            </a>

            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.templates.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Template') }}
            </a>
{{--            <a class="btn btn-info btn-md btn-flat" href="https://editor.digisource.vn/" target="_blank">--}}
{{--                <i class="fa fa-pencil mr-1"></i> Design Template--}}
{{--            </a>--}}
        @endslot
    @endcomponent

    @include('sendportal::templates.partials.grid')

@endsection
