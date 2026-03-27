@extends('sendportal::layouts.app')

@section('title', __('New Level'))

@section('heading')
    {{ __('Levels') }}
@stop

@section('content')
    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Create Level'))

        @slot('cardBody')
            <form action="{{ route('sendportal.levels.store') }}" method="POST" class="form-horizontal">
                @csrf
                @include('sendportal::levels.partials.form')
                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent
@stop
