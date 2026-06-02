@extends('sendportal::layouts.app')

@section('title', __("Edit Template"))

@section('heading')
    {{ __('Edit Template') }}: {{ $template->name }}
@stop

@section('content')

<style>
    /* ----- Template Gallery Modal aesthetic (shared with create.blade.php) ----- */
    #templateGalleryModal .modal-dialog { max-width: 1140px; margin: 1.75rem auto; }
    #templateGalleryModal .modal-content { border: 0; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,.25); overflow: hidden; }
    #templateGalleryModal .modal-header { padding: 18px 24px; border-bottom: 1px solid #eef0f4; background: #fff; }
    #templateGalleryModal .modal-header .modal-title { font-weight: 600; letter-spacing: -.01em; }
    #templateGalleryModal .gallery-tabs { background: #fafbfc; border-bottom: 1px solid #eef0f4; padding: 0 24px; }
    #templateGalleryModal .gallery-tabs .nav-link { border: 0; color: #6c757d; padding: 14px 18px; font-weight: 500; border-bottom: 2px solid transparent; background: transparent; }
    #templateGalleryModal .gallery-tabs .nav-link.active { color: #2c6df0; border-bottom-color: #2c6df0; background: transparent; }
    #templateGalleryModal .modal-body { padding: 0; background: #fff; max-height: 75vh; overflow-y: auto; }
    #templateGalleryModal .tab-pane > .card-body { padding: 22px 24px; }
    #templateGalleryModal .example-card, #templateGalleryModal .market-card { transition: transform .15s ease, box-shadow .15s ease, border-color .15s; border: 1px solid #e9ecef; }
    #templateGalleryModal .example-card:hover, #templateGalleryModal .market-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(20,40,90,.10); border-color: #c3d6fa; }
    #templateGalleryModal .example-card.selected, #templateGalleryModal .market-card.selected { border-color: #2c6df0; box-shadow: 0 0 0 3px rgba(44,109,240,.18); }
    #templateGalleryModal .example-filter { border-radius: 100px; padding: 3px 12px; font-size: 12.5px; }
    #templateGalleryModal .gallery-toolbar { position: sticky; top: 0; z-index: 5; background: #fff; border-bottom: 1px solid #f0f2f5; padding: 12px 24px; }
    #browseTemplatesBtn { border: 1px dashed #c5d4ee; background: #f6f9ff; color: #2c6df0; transition: background .15s, border-color .15s; }
    #browseTemplatesBtn:hover { background: #ebf2ff; border-color: #2c6df0; color: #1a4fc7; }
</style>

<form class="" action="">
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center">
                <label for="id-field-name" class="mb-0 mr-2 font-weight-bold text-nowrap">{{ __('Template Name:') }}</label>
                <input id="id-field-name" class="form-control form-control-lg border-0" name="name" type="text"
                       value="{{ old('name', $template->name ?? '') }}"
                       placeholder="{{ __('Enter template name...') }}" style="box-shadow: none; font-size: 1rem;">
                <span class="text-muted small ml-2 text-nowrap" id="save-status"></span>
                <button type="button" id="browseTemplatesBtn"
                        class="btn btn-md ml-2 text-nowrap"
                        data-toggle="modal" data-target="#templateGalleryModal">
                    <i class="fas fa-th-large mr-1"></i> {{ __('Browse Templates') }}
                </button>
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

{{-- Template Gallery Modal (Examples + Market) --}}
<div class="modal fade" id="templateGalleryModal" tabindex="-1" role="dialog" aria-labelledby="templateGalleryTitle">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="templateGalleryTitle">{{ __('Choose a template') }}</h5>
                    <small class="text-muted">{{ __('Replace the current design with one from your gallery or saved templates.') }}</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="gallery-tabs">
                <ul class="nav nav-tabs border-0" id="templateGalleryTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="examples-tab" data-toggle="tab" href="#examples-panel" role="tab" aria-selected="true">
                            <i class="fas fa-magic mr-1"></i> {{ __('Example Templates') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="market-tab" data-toggle="tab" href="#market-panel" role="tab" aria-selected="false">
                            <i class="fas fa-store mr-1"></i> {{ __('Market') }}
                            <span class="badge badge-light ml-1" id="market-count"></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="examples-panel" role="tabpanel">
                        @include('sendportal::templates.partials.example-templates-content')
                    </div>
                    <div class="tab-pane fade" id="market-panel" role="tabpanel">
                        <div class="gallery-toolbar d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-info-circle mr-1"></i> {{ __('Browse and import from your saved templates') }}
                            </p>
                            <div class="d-flex align-items-center">
                                <input type="text" id="market-search" class="form-control form-control-sm" placeholder="{{ __('Search templates...') }}" style="width: 250px;">
                                <button class="btn btn-sm btn-outline-secondary ml-2" id="market-refresh" title="{{ __('Refresh') }}">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="market-grid" class="row">
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">{{ __('Loading templates...') }}</p>
                                </div>
                            </div>
                            <div id="market-pagination" class="d-flex justify-content-center mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <small class="text-muted mr-auto">
                    <i class="fas fa-keyboard mr-1"></i> {{ __('Tip: click a card to load it into the editor') }}
                </small>
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Market Import Confirmation Modal --}}
<div class="modal fade" id="marketImportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-file-import mr-1"></i> {{ __('Import Template') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <p class="mb-2">{{ __('You are about to import:') }}</p>
                    <h5 id="import-template-name" class="font-weight-bold text-primary"></h5>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    {{ __('This will replace the current editor content. Do you want to continue?') }}
                </div>
                <div id="import-preview" class="border rounded p-2" style="max-height: 300px; overflow: auto; background: #f8f9fa;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirm-import-btn">
                    <i class="fas fa-file-import mr-1"></i> {{ __('Import Template') }}
                </button>
            </div>
        </div>
    </div>
</div>
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

        // =============================================
        // Template Gallery (Examples + Market) wiring
        // =============================================
        $(document).on('click', '.example-card', function() {
            var templateKey = $(this).data('template');
            if (typeof exampleTemplates !== 'undefined' && exampleTemplates[templateKey]) {
                $('.example-card').removeClass('selected');
                $(this).addClass('selected');
                editor.loadDesign(exampleTemplates[templateKey]);

                var label = $(this).find('.card-footer small').text().trim();
                $('#templateGalleryModal').modal('hide');
                $('html, body').animate({ scrollTop: $('#editor-container').offset().top - 80 }, 250);
                toastr.info('Template "' + label + '" {{ __("loaded!") }}');
            }
        });

        $(document).on('click', '.example-filter', function() {
            var category = $(this).data('category');
            $('.example-filter').removeClass('active btn-primary').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('active btn-primary');
            if (category === 'all') {
                $('.example-item').show();
            } else {
                $('.example-item').hide();
                $('.example-item[data-category="' + category + '"]').show();
                $('.example-item[data-category="all"]').show();
            }
        });

        $(document).on('input', '#example-search', function() {
            var search = $(this).val().toLowerCase().trim();
            $('.example-item').each(function() {
                var name = $(this).find('.card-footer small').text().toLowerCase();
                $(this).toggle(search === '' || name.indexOf(search) !== -1);
            });
        });

        // Market tab
        var marketLoaded = false;
        var currentMarketPage = 1;
        var currentMarketSearch = '';
        var pendingImportId = null;

        $('#market-tab').on('shown.bs.tab', function() {
            if (!marketLoaded) { loadMarketTemplates(1, ''); marketLoaded = true; }
        });

        var marketSearchTimer = null;
        $(document).on('input', '#market-search', function() {
            clearTimeout(marketSearchTimer);
            var search = $(this).val();
            marketSearchTimer = setTimeout(function() {
                currentMarketSearch = search;
                loadMarketTemplates(1, search);
            }, 400);
        });

        $(document).on('click', '#market-refresh', function() {
            loadMarketTemplates(currentMarketPage, currentMarketSearch);
        });

        function loadMarketTemplates(page, search) {
            var grid = $('#market-grid');
            grid.html('<div class="col-12 text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i><p class="text-muted">{{ __("Loading templates...") }}</p></div>');
            $.ajax({
                url: "{{ route('sendportal.templates.market') }}",
                type: 'GET',
                data: { page: page, search: search },
                success: function(response) {
                    currentMarketPage = response.current_page;
                    $('#market-count').text(response.total);
                    renderMarketGrid(response.data);
                    renderMarketPagination(response);
                },
                error: function() {
                    grid.html('<div class="col-12 text-center py-5"><i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i><p class="text-muted">{{ __("Failed to load templates") }}</p></div>');
                }
            });
        }

        function renderMarketGrid(templates) {
            var grid = $('#market-grid');
            if (!templates || templates.length === 0) {
                grid.html('<div class="col-12 text-center py-5"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><p class="text-muted">{{ __("No templates found") }}</p></div>');
                return;
            }
            var html = '';
            templates.forEach(function(t) {
                var date = new Date(t.updated_at);
                var dateStr = date.toLocaleDateString('vi-VN');
                var previewHtml = t.content ? t.content : '';
                html += '<div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">';
                html += '  <div class="card h-100 market-card" data-id="' + t.id + '" data-name="' + escapeHtml(t.name) + '" style="cursor:pointer;">';
                html += '    <div class="card-body p-0" style="height: 180px; overflow: hidden; position: relative; background: #f8f9fa;">';
                html += '      <div style="transform: scale(0.35); transform-origin: top left; width: 285%; height: 285%; pointer-events: none;">' + previewHtml + '</div>';
                html += '      <div style="position:absolute; bottom:0; left:0; right:0; height:40px; background: linear-gradient(transparent, #f8f9fa);"></div>';
                html += '    </div>';
                html += '    <div class="card-footer bg-white py-2">';
                html += '      <div class="d-flex justify-content-between align-items-center">';
                html += '        <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width: 70%;">';
                html += '          <small class="font-weight-bold" title="' + escapeHtml(t.name) + '">' + escapeHtml(t.name) + '</small>';
                html += '        </div>';
                html += '        <small class="text-muted">' + dateStr + '</small>';
                html += '      </div>';
                html += '      <div class="mt-1">';
                html += '        <button class="btn btn-sm btn-outline-primary btn-block import-market-btn" data-id="' + t.id + '" data-name="' + escapeHtml(t.name) + '">';
                html += '          <i class="fas fa-file-import mr-1"></i> {{ __("Use Template") }}';
                html += '        </button>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            });
            grid.html(html);
        }

        function renderMarketPagination(response) {
            var pag = $('#market-pagination');
            if (response.last_page <= 1) { pag.html(''); return; }
            var html = '<nav><ul class="pagination pagination-sm">';
            if (response.current_page > 1) {
                html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + (response.current_page - 1) + '">&laquo;</a></li>';
            }
            for (var i = 1; i <= response.last_page; i++) {
                if (i === response.current_page) {
                    html += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
                } else if (i <= 3 || i > response.last_page - 3 || Math.abs(i - response.current_page) <= 2) {
                    html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                } else if (i === 4 || i === response.last_page - 3) {
                    html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            if (response.current_page < response.last_page) {
                html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + (response.current_page + 1) + '">&raquo;</a></li>';
            }
            html += '</ul></nav>';
            pag.html(html);
        }

        $(document).on('click', '.market-page-link', function(e) {
            e.preventDefault();
            loadMarketTemplates($(this).data('page'), currentMarketSearch);
        });

        $(document).on('click', '.import-market-btn', function(e) {
            e.stopPropagation();
            pendingImportId = $(this).data('id');
            var name = $(this).data('name');
            $('#import-template-name').text(name);
            $('#import-preview').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> {{ __("Loading preview...") }}</div>');

            var gallery = $('#templateGalleryModal');
            if (gallery.hasClass('show')) {
                gallery.one('hidden.bs.modal', function () { $('#marketImportModal').modal('show'); }).modal('hide');
            } else {
                $('#marketImportModal').modal('show');
            }

            $.ajax({
                url: "{{ route('sendportal.templates.market.design', '') }}/" + pendingImportId,
                type: 'GET',
                success: function() {
                    $('#import-preview').html('<div class="text-center text-muted py-2"><i class="fas fa-check-circle text-success fa-2x mb-2"></i><br>{{ __("Template design ready to import") }}</div>');
                },
                error: function() {
                    $('#import-preview').html('<div class="text-center text-danger py-2">{{ __("Could not load preview") }}</div>');
                }
            });
        });

        $(document).on('click', '.market-card', function() {
            var btn = $(this).find('.import-market-btn');
            if (btn.length) btn.click();
        });

        $('#confirm-import-btn').click(function() {
            if (!pendingImportId) return;
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Importing...") }}');

            $.ajax({
                url: "{{ route('sendportal.templates.market.design', '') }}/" + pendingImportId,
                type: 'GET',
                success: function(response) {
                    var designJson = typeof response.data_json === 'string' ? JSON.parse(response.data_json) : response.data_json;
                    editor.loadDesign(designJson);
                    $('#marketImportModal').modal('hide');
                    toastr.success('{{ __("Template imported successfully!") }}');
                    $('html, body').animate({ scrollTop: $('#editor-container').offset().top - 80 }, 300);
                },
                error: function() { toastr.error('{{ __("Failed to import template") }}'); },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-file-import mr-1"></i> {{ __("Import Template") }}');
                    pendingImportId = null;
                }
            });
        });

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Auto-focus search on tab when modal opens
        $('#templateGalleryModal').on('shown.bs.modal', function() {
            var activeTab = $('#templateGalleryTabs .nav-link.active').attr('id');
            if (activeTab === 'examples-tab') { $('#example-search').trigger('focus'); }
            else if (activeTab === 'market-tab') { $('#market-search').trigger('focus'); }
        });

        // =============================================
        // Save
        // =============================================
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
