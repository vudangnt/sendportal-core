@extends('sendportal::layouts.app')

@section('heading'){{ __('Transactional Templates') }}@endsection

@section('content')
@php($transactionalTemplates = $templates)
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col"><h3>Transactional Templates</h3></div>
        <div class="col-auto">
            <a href="{{ route('sendportal.templates.transactional.defaults') }}" class="btn btn-light">
                <i class="fas fa-list"></i> Browse defaults
            </a>
            <a href="{{ route('sendportal.templates.transactional.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @include('sendportal::templates.partials.grid-transactional')
</div>
@endsection
