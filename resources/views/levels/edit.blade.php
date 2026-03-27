@extends('sendportal::layouts.app')

@section('title', __('Edit Level'))

@section('heading')
    {{ __('Levels') }}
@stop

@section('content')
    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Edit Level'))

        @slot('cardBody')
            <form action="{{ route('sendportal.levels.update', $level->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                @include('sendportal::levels.partials.form')
                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent
@stop
