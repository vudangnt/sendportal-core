@extends('sendportal::layouts.app')

@section('title', __('Create Campaign'))

@section('heading', __('Campaigns'))

@section('content')

    @if( ! $emailServices)
        <div class="callout callout-danger">
            <h4>{{ __('You haven\'t added any email service!') }}</h4>
            <p>{{ __('Before you can create a campaign, you must first') }} <a
                    href="{{ route('sendportal.email_services.create') }}">{{ __('add an email service') }}</a>.
            </p>
        </div>
    @else
        {{-- Step Indicator --}}
        <div class="campaign-steps mb-4">
            <div class="d-flex align-items-center justify-content-center">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-label">{{ __('Create') }}</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">2</span>
                    <span class="step-label">{{ __('Preview & Send') }}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-plus-circle mr-1"></i> {{ __('Create Campaign') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sendportal.campaigns.store') }}" method="POST" class="form-horizontal">
                            @csrf
                            @include('sendportal::campaigns.partials.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
	@endif
@stop

@push('css')
    <style>
        .campaign-steps { padding: 10px 0; }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            background: #e9ecef;
            color: #6c757d;
            margin-bottom: 4px;
            transition: all 0.3s;
        }
        .step.active .step-number {
            background: #007bff;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.3);
        }
        .step.completed .step-number {
            background: #28a745;
            color: #fff;
        }
        .step-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }
        .step.active .step-label { color: #007bff; font-weight: 600; }
        .step-line {
            width: 80px;
            height: 2px;
            background: #e9ecef;
            margin: 0 12px;
            margin-bottom: 20px;
        }
        .step-line.completed { background: #28a745; }
    </style>
@endpush
