@extends('sendportal::layouts.app')

@section('title', __('Import Template'))

@section('content')
    <div class="card">
        <div class="card-header">
            {{ __('Import Template from JSON') }}
        </div>
        <div class="card-body">
            <form action="{{ route('sendportal.templates.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="jsonFile">{{ __('Upload JSON File') }}</label>
                    <input type="file" id="jsonFile" name="jsonFile" accept=".json" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
            </form>
        </div>
    </div>
@endsection
