@extends('sendportal::layouts.app')

@section('title', __('New Industry'))

@section('heading')
    {{ __('Industries') }}
@stop

@section('content')
    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Create Industry'))

        @slot('cardBody')
            <form action="{{ route('sendportal.industries.store') }}" method="POST" class="form-horizontal">
                @csrf
                @include('sendportal::industries.partials.form')
                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent
@stop
