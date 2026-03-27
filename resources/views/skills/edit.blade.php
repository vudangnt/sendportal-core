@extends('sendportal::layouts.app')

@section('title', __('Edit Skill'))

@section('heading')
    {{ __('Skills') }}
@stop

@section('content')
    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Edit Skill'))

        @slot('cardBody')
            <form action="{{ route('sendportal.skills.update', $skill->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                @include('sendportal::skills.partials.form')
                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent
@stop
