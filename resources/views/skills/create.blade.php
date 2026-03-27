@extends('sendportal::layouts.app')

@section('title', __('New Skill'))

@section('heading')
    {{ __('Skills') }}
@stop

@section('content')
    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Create Skill'))

        @slot('cardBody')
            <form action="{{ route('sendportal.skills.store') }}" method="POST" class="form-horizontal">
                @csrf
                @include('sendportal::skills.partials.form')
                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent
@stop
