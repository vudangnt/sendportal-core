@extends('sendportal::layouts.app')

@section('title', __('New Location'))

@section('heading')
    {{ __('Locations') }}
@stop

@section('content')

    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Create Location'))

        @slot('cardBody')
            <form action="{{ route('sendportal.locations.store') }}" method="POST" class="form-horizontal">
                @csrf

                @include('sendportal::locations.partials.form')

                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
