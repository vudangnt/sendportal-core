@extends('sendportal::layouts.app')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/css/bootstrap-select.min.css">
@endpush

@section('title', __('Import Subscribers'))

@section('heading')
    {{ __('Import Subscribers') }}
@stop

@section('content')

    @if (isset($errors) and count($errors->getBags()))
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->getBags() as $key => $bag)
                            @foreach($bag->all() as $error)
                                <li>{{ $key }} - {{ $error }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Import via CSV, XSLX file'))

        @slot('cardBody')
            <p><b>{{ __('CSV, XSLX format') }}:</b> {{ __('Format your CSV, XSLX the same way as the example below (with the first title row). Use the ID or email columns if you want to update a Subscriber instead of creating it.') }}</p>

            <div class="table-responsive">
                <table class="table table-bordered table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('id') }}</th>
                            <th>{{ __('email') }}</th>
                            <th>{{ __('first_name') }}</th>
                            <th>{{ __('last_name') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>me@sendportal.io</td>
                            <td>Myself</td>
                            <td>Included</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form action="{{ route('sendportal.subscribers.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label for="file" class="col-sm-2 col-form-label">{{ __('CSV File') }}</label>
                    <div class="col-sm-10">
                        <input type="file" name="file" class="form-control-file">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="tags" class="col-sm-2 col-form-label">{{ __('Tags') }}</label>
                    <div class="col-sm-10">
                        <select name="tags[]" id="tags" class="form-control" multiple>
                            @foreach($tags as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('Select tags to apply to imported subscribers') }}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="locations" class="col-sm-2 col-form-label">{{ __('Locations') }}</label>
                    <div class="col-sm-10">
                        <select name="locations[]" id="locations" class="form-control" multiple>
                            @foreach($locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('Select locations to apply to imported subscribers') }}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                    </div>
                </div>
            </form>
        @endSlot
    @endcomponent

@stop


    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script>
