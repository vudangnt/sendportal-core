@extends('sendportal::layouts.app')

@section('title', __("Create Template"))

@section('heading')
    {{ __('Create Template') }}
@stop

@section('content')

    <style>
        /* ----- Template Gallery Modal aesthetic ----- */
        #templateGalleryModal .modal-dialog {
            max-width: 1140px;
            margin: 1.75rem auto;
        }
        #templateGalleryModal .modal-content {
            border: 0;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overflow: hidden;
        }
        #templateGalleryModal .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid #eef0f4;
            background: #fff;
        }
        #templateGalleryModal .modal-header .modal-title {
            font-weight: 600;
            letter-spacing: -.01em;
        }
        #templateGalleryModal .gallery-tabs {
            background: #fafbfc;
            border-bottom: 1px solid #eef0f4;
            padding: 0 24px;
        }
        #templateGalleryModal .gallery-tabs .nav-link {
            border: 0;
            color: #6c757d;
            padding: 14px 18px;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            background: transparent;
        }
        #templateGalleryModal .gallery-tabs .nav-link.active {
            color: #2c6df0;
            border-bottom-color: #2c6df0;
            background: transparent;
        }
        #templateGalleryModal .modal-body {
            padding: 0;
            background: #fff;
            max-height: 75vh;
            overflow-y: auto;
        }
        #templateGalleryModal .tab-pane > .card-body { padding: 22px 24px; }
        #templateGalleryModal .example-card,
        #templateGalleryModal .market-card {
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s;
            border: 1px solid #e9ecef;
        }
        #templateGalleryModal .example-card:hover,
        #templateGalleryModal .market-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(20,40,90,.10);
            border-color: #c3d6fa;
        }
        #templateGalleryModal .example-card.selected,
        #templateGalleryModal .market-card.selected {
            border-color: #2c6df0;
            box-shadow: 0 0 0 3px rgba(44,109,240,.18);
        }
        #templateGalleryModal .example-filter {
            border-radius: 100px;
            padding: 3px 12px;
            font-size: 12.5px;
        }
        #templateGalleryModal .gallery-toolbar {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #fff;
            border-bottom: 1px solid #f0f2f5;
            padding: 12px 24px;
        }
        #templateGalleryModal .browse-empty {
            padding: 60px 20px;
            text-align: center;
            color: #94a3b8;
        }
        #browseTemplatesBtn {
            border: 1px dashed #c5d4ee;
            background: #f6f9ff;
            color: #2c6df0;
            transition: background .15s, border-color .15s;
        }
        #browseTemplatesBtn:hover {
            background: #ebf2ff;
            border-color: #2c6df0;
            color: #1a4fc7;
        }
    </style>

    <form class="" action="">
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <label for="id-field-name" class="mb-0 mr-2 font-weight-bold text-nowrap">{{ __('Template Name:') }}</label>
                    <input id="id-field-name" class="form-control form-control-lg border-0" name="name" type="text"
                           value="{{ old('name', $template->name ?? '') }}"
                           placeholder="{{ __('Enter template name...') }}" style="box-shadow: none; font-size: 1rem;">
                    <button type="button" id="browseTemplatesBtn"
                            class="btn btn-md ml-2 text-nowrap"
                            data-toggle="modal" data-target="#templateGalleryModal">
                        <i class="fas fa-th-large mr-1"></i> {{ __('Browse Templates') }}
                    </button>
                </div>
            </div>
        </div>

        <div style="height: calc(100vh - 300px); min-height: 500px;" id="editor-container"></div>

        <div class="mt-3 d-flex justify-content-between">
            <a href="{{ route('sendportal.templates.index') }}" class="btn btn-light">
                <i class="fa fa-arrow-left mr-1"></i> {{ __('Back to Templates') }}
            </a>
            <div>
                <button id="btn-save-template" class="btn btn-primary btn-md" type="button">
                    <i class="fa fa-save mr-1"></i> {{ __('Save Template') }}
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
                        <h5 class="modal-title mb-0" id="templateGalleryTitle">
                            {{ __('Choose a template') }}
                        </h5>
                        <small class="text-muted">
                            {{ __('Start from a pre-made design or import from your saved templates.') }}
                        </small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="gallery-tabs">
                    <ul class="nav nav-tabs border-0" id="templateGalleryTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="examples-tab" data-toggle="tab"
                               href="#examples-panel" role="tab" aria-controls="examples-panel" aria-selected="true">
                                <i class="fas fa-magic mr-1"></i> {{ __('Example Templates') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="market-tab" data-toggle="tab"
                               href="#market-panel" role="tab" aria-controls="market-panel" aria-selected="false">
                                <i class="fas fa-store mr-1"></i> {{ __('Market') }}
                                <span class="badge badge-light ml-1" id="market-count"></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="modal-body">
                    <div class="tab-content">
                        {{-- Examples Tab --}}
                        <div class="tab-pane fade show active" id="examples-panel" role="tabpanel" aria-labelledby="examples-tab">
                            @include('sendportal::templates.partials.example-templates-content')
                        </div>

                        {{-- Market Tab --}}
                        <div class="tab-pane fade" id="market-panel" role="tabpanel" aria-labelledby="market-tab">
                            <div class="gallery-toolbar d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0 small">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ __('Browse and import from your saved templates') }}
                                </p>
                                <div class="d-flex align-items-center">
                                    <input type="text" id="market-search" class="form-control form-control-sm"
                                           placeholder="{{ __('Search templates...') }}" style="width: 250px;">
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
                        <i class="fas fa-keyboard mr-1"></i>
                        {{ __('Tip: click a card to load it into the editor') }}
                    </small>
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        {{ __('Close') }}
                    </button>
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

        // Load default or existing template
        @if(isset($template) && $template->data_json)
            editor.loadDesign({!! $template->data_json !!});
        @else
            editor.loadDesign(exampleTemplates.blank);
        @endif

        // Image upload callback — Unlayer fires this when the user picks/drops
        // an image. If we don't return a URL, Unlayer's fallback tries to
        // inline the image as a data URL which blows up for large files
        // ("Base64 string is too long for URL"). Defensive guards + clear
        // errors so failures surface in console instead of cascading.
        editor.registerCallback('image', function(file, done) {
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
                if (!response.ok) {
                    throw new Error('[Unlayer upload] HTTP ' + response.status + ' ' + response.statusText);
                }
                return response.json();
            }).then(function(payload) {
                if (!payload || !payload.filelink) {
                    throw new Error('[Unlayer upload] response missing filelink: ' + JSON.stringify(payload));
                }
                done({ progress: 100, url: payload.filelink });
            }).catch(function(err) {
                console.error(err);
                if (window.toastr) toastr.error('Image upload failed — see console for details.');
                // Signal completion to Unlayer with empty url so it doesn't
                // recurse into the base64 fallback.
                done({ progress: 100, url: '' });
            });
        });

        // =============================================
        // Example template selection (existing logic)
        // =============================================
        $(document).on('click', '.example-card', function() {
            var templateKey = $(this).data('template');
            if (exampleTemplates[templateKey]) {
                $('.example-card').removeClass('selected');
                $(this).addClass('selected');
                editor.loadDesign(exampleTemplates[templateKey]);

                var label = $(this).find('.card-footer small').text().trim();
                var nameField = $('#id-field-name');
                if (!nameField.val()) {
                    nameField.val(label);
                }

                $('#templateGalleryModal').modal('hide');
                $('html, body').animate({ scrollTop: $('#editor-container').offset().top - 80 }, 250);
                toastr.info('Template "' + label + '" {{ __("loaded!") }}');
            }
        });

        // Category filter
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

        // Search examples
        $('#example-search').on('input', function() {
            var search = $(this).val().toLowerCase().trim();
            $('.example-item').each(function() {
                var name = $(this).find('.card-footer small').text().toLowerCase();
                $(this).toggle(search === '' || name.indexOf(search) !== -1);
            });
        });

        // Toggle examples
        $('#toggle-examples').click(function() {
            var body = $('#examples-body');
            body.slideToggle(200);
            $(this).find('i').toggleClass('fa-chevron-up fa-chevron-down');
        });

        // =============================================
        // Market tab logic
        // =============================================
        var marketLoaded = false;
        var currentMarketPage = 1;
        var currentMarketSearch = '';
        var pendingImportId = null;

        // Load market templates when its tab is shown
        $('#market-tab').on('shown.bs.tab', function() {
            if (!marketLoaded) {
                loadMarketTemplates(1, '');
                marketLoaded = true;
            }
        });

        // Reset hover state when gallery closes (cards keep transformed style otherwise)
        $('#templateGalleryModal').on('hidden.bs.modal', function () {
            $('.market-card').css({ transform: '', boxShadow: '' });
        });

        // a11y workaround: when Bootstrap closes a modal while it (or a child)
        // still has focus, Chrome warns "Blocked aria-hidden on element with
        // focused descendant". Move focus out BEFORE BS sets aria-hidden,
        // and use the `inert` attribute (which actually blocks focus) instead
        // of aria-hidden where supported.
        $(document).on('hide.bs.modal', '.modal', function () {
            var modal = this;
            if (modal === document.activeElement || modal.contains(document.activeElement)) {
                // Park focus on body so BS's pending aria-hidden never traps a focused descendant.
                if (document.activeElement && document.activeElement.blur) document.activeElement.blur();
                if (document.body && document.body.focus) {
                    // Bootstrap's modal has tabindex=-1 — body usually doesn't, so don't try to focus it.
                    // Just blur is enough.
                }
            }
        });
        $(document).on('hidden.bs.modal', '.modal', function () {
            // BS has just set aria-hidden="true". Use inert (modern, no warning)
            // and drop aria-hidden — better a11y posture.
            this.removeAttribute('aria-hidden');
            this.setAttribute('inert', '');
        });
        $(document).on('show.bs.modal', '.modal', function () {
            this.removeAttribute('inert');
        });

        // Auto-focus the search input on the active tab when modal shows
        $('#templateGalleryModal').on('shown.bs.modal', function () {
            var activeTab = $('#templateGalleryTabs .nav-link.active').attr('id');
            if (activeTab === 'examples-tab') {
                $('#example-search').trigger('focus');
            } else if (activeTab === 'market-tab') {
                $('#market-search').trigger('focus');
            }
        });

        // Search in market
        var marketSearchTimer = null;
        $('#market-search').on('input', function() {
            clearTimeout(marketSearchTimer);
            var search = $(this).val();
            marketSearchTimer = setTimeout(function() {
                currentMarketSearch = search;
                loadMarketTemplates(1, search);
            }, 400);
        });

        // Refresh market
        $('#market-refresh').click(function() {
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

                // Generate a preview from content HTML (truncated)
                var previewHtml = t.content ? t.content : '';

                html += '<div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">';
                html += '  <div class="card h-100 market-card" data-id="' + t.id + '" data-name="' + escapeHtml(t.name) + '" style="cursor:pointer; transition: transform 0.15s ease, box-shadow 0.15s ease;">';
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
            if (response.last_page <= 1) {
                pag.html('');
                return;
            }

            var html = '<nav><ul class="pagination pagination-sm">';

            // Previous
            if (response.current_page > 1) {
                html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + (response.current_page - 1) + '">&laquo;</a></li>';
            }

            // Pages
            for (var i = 1; i <= response.last_page; i++) {
                if (i === response.current_page) {
                    html += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
                } else if (i <= 3 || i > response.last_page - 3 || Math.abs(i - response.current_page) <= 2) {
                    html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                } else if (i === 4 || i === response.last_page - 3) {
                    html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // Next
            if (response.current_page < response.last_page) {
                html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + (response.current_page + 1) + '">&raquo;</a></li>';
            }

            html += '</ul></nav>';
            pag.html(html);
        }

        // Pagination click
        $(document).on('click', '.market-page-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            loadMarketTemplates(page, currentMarketSearch);
        });

        // Market card hover
        $(document).on('mouseenter', '.market-card', function() {
            $(this).css({ transform: 'translateY(-3px)', boxShadow: '0 4px 12px rgba(0,0,0,0.15)' });
        }).on('mouseleave', '.market-card', function() {
            $(this).css({ transform: '', boxShadow: '' });
        });

        // Import button click - close gallery, then show confirmation modal
        $(document).on('click', '.import-market-btn', function(e) {
            e.stopPropagation();
            pendingImportId = $(this).data('id');
            var name = $(this).data('name');
            $('#import-template-name').text(name);
            $('#import-preview').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> {{ __("Loading preview...") }}</div>');

            // Close gallery first to avoid nested modal stacking issues
            var gallery = $('#templateGalleryModal');
            if (gallery.hasClass('show')) {
                gallery.one('hidden.bs.modal', function () {
                    $('#marketImportModal').modal('show');
                }).modal('hide');
            } else {
                $('#marketImportModal').modal('show');
            }

            // Load preview
            $.ajax({
                url: "{{ route('sendportal.templates.market.design', '') }}/" + pendingImportId,
                type: 'GET',
                success: function(response) {
                    // Try to show a basic preview from the template
                    $('#import-preview').html('<div class="text-center text-muted py-2"><i class="fas fa-check-circle text-success fa-2x mb-2"></i><br>{{ __("Template design ready to import") }}</div>');
                },
                error: function() {
                    $('#import-preview').html('<div class="text-center text-danger py-2">{{ __("Could not load preview") }}</div>');
                }
            });
        });

        // Also clicking on the card itself
        $(document).on('click', '.market-card', function() {
            var btn = $(this).find('.import-market-btn');
            if (btn.length) btn.click();
        });

        // Confirm import
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

                    // Set name
                    var nameField = $('#id-field-name');
                    if (!nameField.val()) {
                        nameField.val(response.name + ' (copy)');
                    }

                    $('#marketImportModal').modal('hide');
                    toastr.success('{{ __("Template imported successfully!") }}');

                    // Scroll to editor
                    $('html, body').animate({ scrollTop: $('#editor-container').offset().top - 80 }, 300);
                },
                error: function() {
                    toastr.error('{{ __("Failed to import template") }}');
                },
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

        // =============================================
        // Save template
        // =============================================
        $("#btn-save-template").click(function() {
            saveTemplate();
        });

        function saveTemplate() {
            var name = $('#id-field-name').val();
            if (!name || !name.trim()) {
                toastr.error('{{ __("Please enter a template name") }}');
                $('#id-field-name').focus();
                return;
            }

            var btn = $('#btn-save-template');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Saving...") }}');

            editor.exportHtml(function(data) {
                var json = data.design;
                var html = data.html;

                $.ajax({
                    url: "/templates",
                    type: 'POST',
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        '_token': "{{ csrf_token() }}",
                        'name': name.trim(),
                        'content': html,
                        'data_json': JSON.stringify(json)
                    }),
                    success: function(response) {
                        toastr.success('{{ __("Template saved successfully!") }}');
                        window.location.href = "{{ route('sendportal.templates.index') }}";
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> {{ __("Save Template") }}');
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
