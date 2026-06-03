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
                    <i class="fas fa-paper-plane"></i> {{ __('Send test') }}
                </button>
            @endif
            <button id="btn-save-tx-template" class="btn btn-primary" type="button">
                <i class="fa fa-save mr-1"></i> {{ __('Save') }}
                <span id="tx-save-status" class="ml-2 small"></span>
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="tx-tpl-form" action="{{ $action }}" method="POST">
        @csrf
        @if($method !== 'POST')@method($method)@endif

        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="small text-muted mb-1">{{ __('Code *') }}</label>
                            <input type="text" name="code" class="form-control" required
                                   pattern="[a-z0-9_-]+"
                                   value="{{ old('code', $template->code ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="small text-muted mb-1">{{ __('Name *') }}</label>
                            <input type="text" name="name" class="form-control" required
                                   value="{{ old('name', $template->name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="small text-muted mb-1">
                                {{ __('Subject') }}
                                <span class="text-muted">— {{ __('supports') }} <code>@{{ var }}</code></span>
                            </label>
                            <input type="text" name="subject" class="form-control"
                                   value="{{ old('subject', $template->subject ?? '') }}"
                                   placeholder="Hi @{{ candidate_name }}, your application…">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden inputs populated by Unlayer's exportHtml before submit --}}
        <input type="hidden" name="content"   id="tx-content-input">
        <input type="hidden" name="data_json" id="tx-data-json-input">
    </form>

    <div style="height: calc(100vh - 290px); min-height: 500px;" id="tx-editor-container"></div>

    @if($template)
        @include('sendportal::templates.transactional.partials.test-modal', [
            'template' => $template,
            'route' => route('sendportal.templates.transactional.test', $template->id),
        ])
    @endif
</div>
@endsection

@push('js')
<script src="//editor.unlayer.com/embed.js"></script>
<script>
    var txEditor = unlayer.createEditor({
        id: 'tx-editor-container',
        projectId: 1234,
        displayMode: 'email',
        locale: 'vi-VN',
        tools: {
            social: { enabled: true },
            timer: { enabled: true },
            video: { enabled: true }
        },
        appearance: { theme: 'light' },
        // Pre-populate merge tags for common job-application variables.
        // Note: @{{ }} escapes Blade — Unlayer sees literal {{var}}.
        mergeTags: {
            candidate_name: { name: 'Candidate Name', value: '@{{candidate_name}}' },
            job_title:      { name: 'Job Title',      value: '@{{job_title}}' },
            company:        { name: 'Company',        value: '@{{company}}' },
            interview_link: { name: 'Interview Link', value: '@{{interview_link}}' },
            interview_date: { name: 'Interview Date', value: '@{{interview_date}}' },
            offer_url:      { name: 'Offer URL',      value: '@{{offer_url}}' },
            recruiter_name: { name: 'Recruiter Name', value: '@{{recruiter_name}}' }
        }
    });

    // Load existing design or start blank
    @if($template && $template->data_json)
        txEditor.loadDesign({!! $template->data_json !!});
    @else
        txEditor.loadBlank();
    @endif

    // Image upload (same defensive pattern as campaign editor)
    txEditor.registerCallback('image', function(file, done) {
        var picked = file && file.attachments && file.attachments[0];
        if (!picked || !(picked instanceof Blob)) {
            console.warn('[Unlayer] image callback received no file', file);
            done({ progress: 100, url: '' });
            return;
        }
        var data = new FormData();
        data.append('file', picked);
        data.append('_token', "{{ csrf_token() }}");
        fetch('/uploads', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: data
        }).then(function(response) {
            if (!response.ok) throw new Error('[Unlayer upload] HTTP ' + response.status);
            return response.json();
        }).then(function(payload) {
            if (!payload || !payload.filelink) throw new Error('[Unlayer upload] response missing filelink');
            done({ progress: 100, url: payload.filelink });
        }).catch(function(err) {
            console.error(err);
            if (window.toastr) toastr.error('Image upload failed — see console.');
            done({ progress: 100, url: '' });
        });
    });

    $('#btn-save-tx-template').on('click', function() {
        var $btn = $(this);
        var $form = $('#tx-tpl-form');
        var $status = $('#tx-save-status');

        if (!$form[0].checkValidity()) {
            $form[0].reportValidity();
            return;
        }

        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Saving...") }}');
        $status.text('').css('color', '');

        txEditor.exportHtml(function(data) {
            $('#tx-content-input').val(data.html || '');
            $('#tx-data-json-input').val(JSON.stringify(data.design || {}));
            $form.submit();
        });
    });

    // Keyboard: Ctrl/Cmd+S
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            $('#btn-save-tx-template').click();
        }
    });

    // a11y: blur before BS toggles aria-hidden + swap to inert
    $(document).on('hide.bs.modal', '.modal', function () {
        if (document.activeElement && (this === document.activeElement || this.contains(document.activeElement))) {
            if (document.activeElement.blur) document.activeElement.blur();
        }
    });
    $(document).on('hidden.bs.modal', '.modal', function () {
        this.removeAttribute('aria-hidden');
        this.setAttribute('inert', '');
    });
    $(document).on('show.bs.modal', '.modal', function () {
        this.removeAttribute('inert');
    });
</script>
@endpush
