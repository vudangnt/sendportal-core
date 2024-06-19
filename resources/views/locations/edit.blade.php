@extends('sendportal::layouts.app')

@section('title', __("Edit Locations"))

@section('heading')
    {{ __('Locations') }}
@stop

@section('content')

    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Edit Location'))

        @slot('cardBody')
            <form action="{{ route('sendportal.locations.update', $location['id']) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')

                @include('sendportal::locations.partials.form')

                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
