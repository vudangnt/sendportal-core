@extends('sendportal::layouts.app')
@section('heading'){{ $template ? __('Edit Template') : __('New Template') }}@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col">
            <a href="{{ url('/templates#transactional') }}" class="btn btn-light btn-sm mb-2">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Templates') }}
            </a>
            <h3 class="mb-0">
                {{ $template ? __('Edit') : __('New') }} {{ __('Transactional Template') }}
                @if($template && $template->code)
                    <code class="ml-2 px-2 py-1 bg-light text-info" style="font-size: 16px; border-radius: 4px;">{{ $template->code }}</code>
                @endif
            </h3>
        </div>
        <div class="col-auto">
            @if($template)
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#sendTestModal">
                <i class="fas fa-paper-plane"></i> Send test
            </button>
            @endif
            <button type="submit" form="tpl-form" class="btn btn-primary">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="tpl-form" action="{{ $action }}" method="POST">
        @csrf
        @if($method !== 'POST')@method($method)@endif
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Code *</label>
                            <input type="text" name="code" class="form-control" required
                                   pattern="[a-z0-9_-]+"
                                   value="{{ old('code', $template->code ?? '') }}">
                            <small class="text-muted">If you use a code that matches a default, this overrides it for the workspace.</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" name="name" class="form-control" required
                                   value="{{ old('name', $template->name ?? '') }}">
                        </div>
                    </div>
                </div>

                @include('sendportal::templates.transactional.partials.preview-pane')
            </div>
        </div>
    </form>

    @if($template)
        @include('sendportal::templates.transactional.partials.test-modal', [
            'template' => $template,
            'route' => route('sendportal.templates.transactional.test', $template->id),
        ])
    @endif
</div>
@endsection
