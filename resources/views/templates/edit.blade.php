@extends('sendportal::layouts.app')

@section('title', __("Edit Template"))

@section('heading')
    {{ __('Edit Template') }}: {{ $template->name }}
@stop

@section('content')

<form class="" action="">
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center">
                <label for="id-field-name" class="mb-0 mr-2 font-weight-bold text-nowrap">{{ __('Template Name:') }}</label>
                <input id="id-field-name" class="form-control form-control-lg border-0" name="name" type="text"
                       value="{{ old('name', $template->name ?? '') }}"
                       placeholder="{{ __('Enter template name...') }}" style="box-shadow: none; font-size: 1rem;">
                <span class="text-muted small ml-2 text-nowrap" id="save-status"></span>
            </div>
        </div>
    </div>

    <div style="height: calc(100vh - 280px); min-height: 500px;" id="editor-container"></div>

    <div class="mt-3 d-flex justify-content-between">
        <a href="{{ route('sendportal.templates.index') }}" class="btn btn-light">
            <i class="fa fa-arrow-left mr-1"></i> {{ __('Back to Templates') }}
        </a>
        <div>
            <button id="btn-save-template" class="btn btn-primary btn-md" type="button">
                <i class="fa fa-save mr-1"></i> {{ __('Save') }}
            </button>
            <button id="btn-save-template_close" class="btn btn-success btn-md" type="button">
                <i class="fa fa-check mr-1"></i> {{ __('Save & Close') }}
            </button>
        </div>
    </div>
</form>
@stop

@push('js')
    <script src="//editor.unlayer.com/embed.js"></script>

    <script>
        var editor = unlayer.createEditor({
            id: 'editor-container',
            projectId: 1234,
            displayMode: 'email',
            locale: 'vi-VN',
            tools: {
                social: { enabled: true },
                timer: { enabled: true },
                video: { enabled: true }
            },
            appearance: {
                theme: 'light'
            }
        });

        editor.loadDesign({!! $template->data_json ?? '{}' !!});

        // Image upload callback
        editor.registerCallback('image', function(file, done) {
            var data = new FormData();
            data.append('file', file.attachments[0]);
            data.append('_token', "{{ csrf_token() }}");

            fetch('/uploads', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: data
            }).then(function(response) {
                if (response.status >= 200 && response.status < 300) return response;
                var error = new Error(response.statusText);
                error.response = response;
                throw error;
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                done({ progress: 100, url: data.filelink });
            });
        });

        $("#btn-save-template").click(function() {
            saveTemplate(false);
        });

        $("#btn-save-template_close").click(function() {
            saveTemplate(true);
        });

        // Keyboard shortcut: Ctrl+S to save
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveTemplate(false);
            }
        });

        function saveTemplate(redirect) {
            var name = $('#id-field-name').val();
            if (!name || !name.trim()) {
                toastr.error('{{ __("Please enter a template name") }}');
                $('#id-field-name').focus();
                return;
            }

            var saveBtn = redirect ? $('#btn-save-template_close') : $('#btn-save-template');
            var origHtml = saveBtn.html();
            saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Saving...") }}');
            $('#save-status').text('{{ __("Saving...") }}').css('color', '#999');

            editor.exportHtml(function(data) {
                var json = data.design;
                var html = data.html;

                $.ajax({
                    url: "{{ route('sendportal.templates.update', $template->id) }}",
                    type: 'PUT',
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        '_token': "{{ csrf_token() }}",
                        'name': name.trim(),
                        'content': html,
                        'data_json': JSON.stringify(json)
                    }),
                    success: function(response) {
                        saveBtn.prop('disabled', false).html(origHtml);
                        if (redirect) {
                            toastr.success('{{ __("Template saved!") }}');
                            window.location.href = "{{ route('sendportal.templates.index') }}";
                        } else {
                            var now = new Date();
                            var time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
                            $('#save-status').text('✓ {{ __("Saved at") }} ' + time).css('color', '#28a745');
                            toastr.success('{{ __("Template updated successfully!") }}');
                        }
                    },
                    error: function(xhr) {
                        saveBtn.prop('disabled', false).html(origHtml);
                        $('#save-status').text('✗ {{ __("Save failed") }}').css('color', '#dc3545');
                        try {
                            var err = JSON.parse(xhr.responseText);
                            toastr.error(JSON.stringify(err.errors));
                        } catch(e) {
                            toastr.error('{{ __("An error occurred while saving.") }}');
                        }
                    }
                });
            });
        }
    </script>
@endpush
