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
            <button type="button" id="browseTemplatesBtn"
                    class="btn btn-md text-nowrap mr-1"
                    data-toggle="modal" data-target="#templateGalleryModal">
                <i class="fas fa-th-large mr-1"></i> {{ __('Browse Templates') }}
            </button>
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
                                   pattern="[a-z0-9 _-]+"
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

    {{-- ====================================================== --}}
    {{-- Template Gallery Modal (Examples + Market)              --}}
    {{-- ====================================================== --}}
    <style>
        #templateGalleryModal .modal-dialog { max-width: 1140px; margin: 1.75rem auto; }
        #templateGalleryModal .modal-content { border: 0; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,.25); overflow: hidden; }
        #templateGalleryModal .modal-header { padding: 18px 24px; border-bottom: 1px solid #eef0f4; background: #fff; }
        #templateGalleryModal .modal-header .modal-title { font-weight: 600; letter-spacing: -.01em; }
        #templateGalleryModal .gallery-tabs { background: #fafbfc; border-bottom: 1px solid #eef0f4; padding: 0 24px; }
        #templateGalleryModal .gallery-tabs .nav-link { border: 0; color: #6c757d; padding: 14px 18px; font-weight: 500; border-bottom: 2px solid transparent; background: transparent; }
        #templateGalleryModal .gallery-tabs .nav-link.active { color: #2c6df0; border-bottom-color: #2c6df0; background: transparent; }
        #templateGalleryModal .modal-body { padding: 0; background: #fff; max-height: 75vh; overflow-y: auto; }
        #templateGalleryModal .tab-pane > .card-body { padding: 22px 24px; }
        #templateGalleryModal .example-card, #templateGalleryModal .market-card { transition: transform .15s, box-shadow .15s, border-color .15s; border: 1px solid #e9ecef; }
        #templateGalleryModal .example-card:hover, #templateGalleryModal .market-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(20,40,90,.10); border-color: #c3d6fa; }
        #templateGalleryModal .example-card.selected, #templateGalleryModal .market-card.selected { border-color: #2c6df0; box-shadow: 0 0 0 3px rgba(44,109,240,.18); }
        #templateGalleryModal .example-filter { border-radius: 100px; padding: 3px 12px; font-size: 12.5px; }
        #templateGalleryModal .gallery-toolbar { position: sticky; top: 0; z-index: 5; background: #fff; border-bottom: 1px solid #f0f2f5; padding: 12px 24px; }
        #browseTemplatesBtn { border: 1px dashed #c5d4ee; background: #f6f9ff; color: #2c6df0; transition: background .15s, border-color .15s; }
        #browseTemplatesBtn:hover { background: #ebf2ff; border-color: #2c6df0; color: #1a4fc7; }
    </style>

    <div class="modal fade" id="templateGalleryModal" tabindex="-1" role="dialog" aria-labelledby="templateGalleryTitle">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0" id="templateGalleryTitle">{{ __('Choose a template') }}</h5>
                        <small class="text-muted">{{ __('Start from a pre-made design or import from your saved templates.') }}</small>
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
                                    <i class="fas fa-info-circle mr-1"></i> {{ __('Browse and import from your saved campaign templates') }}
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

    // ============================================
    // Template Gallery (Examples + Market) → loads design into txEditor
    // ============================================
    $(document).on('click', '.example-card', function() {
        var key = $(this).data('template');
        if (typeof exampleTemplates === 'undefined' || !exampleTemplates[key]) return;
        $('.example-card').removeClass('selected');
        $(this).addClass('selected');
        txEditor.loadDesign(exampleTemplates[key]);

        var label = $(this).find('.card-footer small').text().trim();
        if (label && !$('input[name="name"]').val()) {
            $('input[name="name"]').val(label);
        }
        $('#templateGalleryModal').modal('hide');
        toastr.info('Template "' + label + '" {{ __("loaded!") }}');
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

    // Market tab — uses /templates/api/market (campaign-kind only, scoped by tenant)
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
            type: 'GET', data: { page: page, search: search },
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
            var dateStr = new Date(t.updated_at).toLocaleDateString('vi-VN');
            var previewHtml = t.content ? t.content : '';
            html += '<div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">';
            html += '  <div class="card h-100 market-card" data-id="' + t.id + '" data-name="' + escapeHtml(t.name) + '" style="cursor:pointer;">';
            html += '    <div class="card-body p-0" style="height: 180px; overflow: hidden; position: relative; background: #f8f9fa;">';
            html += '      <div style="transform: scale(0.35); transform-origin: top left; width: 285%; height: 285%; pointer-events: none;">' + previewHtml + '</div>';
            html += '      <div style="position:absolute; bottom:0; left:0; right:0; height:40px; background: linear-gradient(transparent, #f8f9fa);"></div>';
            html += '    </div>';
            html += '    <div class="card-footer bg-white py-2">';
            html += '      <div class="d-flex justify-content-between align-items-center">';
            html += '        <small class="font-weight-bold text-truncate" style="max-width:70%;" title="' + escapeHtml(t.name) + '">' + escapeHtml(t.name) + '</small>';
            html += '        <small class="text-muted">' + dateStr + '</small>';
            html += '      </div>';
            html += '      <button class="btn btn-sm btn-outline-primary btn-block mt-1 import-market-btn" data-id="' + t.id + '" data-name="' + escapeHtml(t.name) + '">';
            html += '        <i class="fas fa-file-import mr-1"></i> {{ __("Use Template") }}';
            html += '      </button>';
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
        if (response.current_page > 1) html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + (response.current_page - 1) + '">&laquo;</a></li>';
        for (var i = 1; i <= response.last_page; i++) {
            if (i === response.current_page) {
                html += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
            } else if (i <= 3 || i > response.last_page - 3 || Math.abs(i - response.current_page) <= 2) {
                html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
            } else if (i === 4 || i === response.last_page - 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        if (response.current_page < response.last_page) html += '<li class="page-item"><a class="page-link market-page-link" href="#" data-page="' + (response.current_page + 1) + '">&raquo;</a></li>';
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
        $('#import-template-name').text($(this).data('name'));
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
            success: function() { $('#import-preview').html('<div class="text-center text-muted py-2"><i class="fas fa-check-circle text-success fa-2x mb-2"></i><br>{{ __("Template design ready to import") }}</div>'); },
            error: function() { $('#import-preview').html('<div class="text-center text-danger py-2">{{ __("Could not load preview") }}</div>'); }
        });
    });

    $(document).on('click', '.market-card', function() {
        var btn = $(this).find('.import-market-btn');
        if (btn.length) btn.click();
    });

    $('#confirm-import-btn').on('click', function() {
        if (!pendingImportId) return;
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Importing...") }}');

        $.ajax({
            url: "{{ route('sendportal.templates.market.design', '') }}/" + pendingImportId,
            type: 'GET',
            success: function(response) {
                var designJson = typeof response.data_json === 'string' ? JSON.parse(response.data_json) : response.data_json;
                txEditor.loadDesign(designJson);
                $('#marketImportModal').modal('hide');
                toastr.success('{{ __("Template imported successfully!") }}');
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

    // Reset hover state + auto-focus search when modal toggles
    $('#templateGalleryModal').on('hidden.bs.modal', function () {
        $('.market-card').css({ transform: '', boxShadow: '' });
    });
    $('#templateGalleryModal').on('shown.bs.modal', function () {
        var activeTab = $('#templateGalleryTabs .nav-link.active').attr('id');
        if (activeTab === 'examples-tab') $('#example-search').trigger('focus');
        else if (activeTab === 'market-tab') $('#market-search').trigger('focus');
    });
</script>
@endpush
