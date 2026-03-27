@extends('sendportal::layouts.app')

@section('title', __('Edit Industry'))

@section('heading')
    {{ __('Industries') }}
@stop

@section('content')
    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Edit Industry'))

        @slot('cardBody')
            <form action="{{ route('sendportal.industries.update', $industry->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                @include('sendportal::industries.partials.form')
                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent
@stop
