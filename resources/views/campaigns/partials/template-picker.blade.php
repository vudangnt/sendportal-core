{{-- Template Picker with Search and Thumbnail Preview --}}
@php
    $selectedTemplateId = $campaign->template_id ?? old('template_id');
    $templateCollection = $templateModels ?? collect();
@endphp

<div class="form-group row">
    <label class="col-sm-3 col-form-label">{{ __('Template') }}</label>
    <div class="col-sm-9">
        <input type="hidden" name="template_id" id="template_id_input" value="{{ $selectedTemplateId }}">

        {{-- Custom dropdown trigger --}}
        <div class="tp-picker" id="templatePicker">
            <div class="tp-selected" id="tpSelected" tabindex="0">
                <div class="tp-selected-inner">
                    <div class="tp-selected-thumb" id="tpSelectedThumb">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="9" y1="21" x2="9" y2="9"/>
                        </svg>
                    </div>
                    <span class="tp-selected-name" id="tpSelectedName">{{ __('- None -') }}</span>
                </div>
                <svg class="tp-arrow" width="12" height="12" viewBox="0 0 12 12" fill="#6c757d">
                    <path d="M2 4l4 4 4-4"/>
                </svg>
            </div>

            {{-- Dropdown --}}
            <div class="tp-dropdown" id="tpDropdown">
                <div class="tp-search-wrap">
                    <input type="text" class="tp-search" id="tpSearch" placeholder="{{ __('Search templates...') }}" autocomplete="off">
                </div>
                <div class="tp-list" id="tpList">
                    {{-- None option --}}
                    <div class="tp-item" data-id="" data-name="{{ __('- None -') }}" data-content="">
                        <div class="tp-item-thumb tp-item-thumb-none">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5">
                                <circle cx="12" cy="12" r="9"/>
                                <line x1="7" y1="7" x2="17" y2="17"/>
                            </svg>
                        </div>
                        <div class="tp-item-info">
                            <div class="tp-item-name">{{ __('- None -') }}</div>
                            <div class="tp-item-desc">{{ __('No template') }}</div>
                        </div>
                    </div>
                    {{-- Template items --}}
                    @foreach($templateCollection as $template)
                        <div class="tp-item" data-id="{{ $template->id }}" data-name="{{ $template->name }}" data-content="{{ base64_encode($template->content ?? '') }}">
                            <div class="tp-item-info">
                                <div class="tp-item-name">{{ $template->name }}</div>
                                <div class="tp-item-desc">{{ __('Created') }} {{ $template->created_at ? $template->created_at->diffForHumans() : '' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Preview area --}}
        <div class="tp-preview-wrap" id="tpPreviewWrap" style="display:none;">
            <div class="tp-preview-header">
                <span>{{ __('Template Preview') }}</span>
                <button type="button" class="tp-preview-close" id="tpPreviewClose">&times;</button>
            </div>
            <iframe id="tpPreviewIframe" class="tp-preview-iframe" sandbox="allow-same-origin" scrolling="yes"></iframe>
        </div>

        @error('template_id')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>
</div>

@push('css')
<style>
/* Template Picker Container */
.tp-picker {
    position: relative;
}

/* Selected display */
.tp-selected {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
    min-height: 42px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.tp-selected:hover {
    border-color: #80bdff;
}
.tp-selected:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    outline: none;
}
.tp-selected-inner {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow: hidden;
}
.tp-selected-thumb {
    width: 40px;
    height: 30px;
    border: 1px solid #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: #f8f9fa;
}
.tp-selected-thumb iframe {
    width: 400px;
    height: 300px;
    transform: scale(0.1);
    transform-origin: 0 0;
    pointer-events: none;
    border: none;
}
.tp-selected-name {
    font-size: 14px;
    color: #495057;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.tp-arrow {
    flex-shrink: 0;
    transition: transform 0.2s;
}
.tp-picker.open .tp-arrow {
    transform: rotate(180deg);
}

/* Dropdown */
.tp-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    background: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    max-height: 350px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.tp-picker.open .tp-dropdown {
    display: flex;
}
.tp-dropdown {
    display: none;
}

/* Search */
.tp-search-wrap {
    padding: 8px;
    border-bottom: 1px solid #e9ecef;
    flex-shrink: 0;
}
.tp-search {
    width: 100%;
    padding: 6px 10px 6px 32px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    font-size: 13px;
    background: #f8f9fa url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") 10px center no-repeat;
    outline: none;
    transition: border-color 0.15s;
}
.tp-search:focus {
    border-color: #80bdff;
    background-color: #fff;
}

/* List */
.tp-list {
    overflow-y: auto;
    max-height: 290px;
    flex: 1;
}

/* Item */
.tp-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.1s;
}
.tp-item:last-child {
    border-bottom: none;
}
.tp-item:hover {
    background: #f0f7ff;
}
.tp-item.active {
    background: #e8f0fe;
}

/* Item thumbnail */
.tp-item-thumb {
    width: 80px;
    height: 60px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
    flex-shrink: 0;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.tp-item-thumb-none {
    background: #f0f0f0;
}
.tp-thumb-iframe {
    width: 800px;
    height: 600px;
    transform: scale(0.1);
    transform-origin: 0 0;
    pointer-events: none;
    border: none;
    position: absolute;
    top: 0;
    left: 0;
}
.tp-item-info {
    flex: 1;
    overflow: hidden;
}
.tp-item-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.tp-item-desc {
    font-size: 11px;
    color: #999;
    margin-top: 2px;
}
.tp-item-hidden {
    display: none !important;
}

/* Preview panel */
.tp-preview-wrap {
    margin-top: 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.tp-preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 14px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-size: 13px;
    font-weight: 500;
    color: #495057;
}
.tp-preview-close {
    background: none;
    border: none;
    font-size: 20px;
    color: #999;
    cursor: pointer;
    padding: 0 4px;
    line-height: 1;
}
.tp-preview-close:hover {
    color: #333;
}
.tp-preview-iframe {
    width: 100%;
    height: 350px;
    border: none;
}
</style>
@endpush

@push('js')
<script>
$(function() {
    const picker = document.getElementById('templatePicker');
    const selected = document.getElementById('tpSelected');
    const dropdown = document.getElementById('tpDropdown');
    const searchInput = document.getElementById('tpSearch');
    const list = document.getElementById('tpList');
    const hiddenInput = document.getElementById('template_id_input');
    const selectedName = document.getElementById('tpSelectedName');
    const selectedThumb = document.getElementById('tpSelectedThumb');
    const previewWrap = document.getElementById('tpPreviewWrap');
    const previewIframe = document.getElementById('tpPreviewIframe');
    const previewClose = document.getElementById('tpPreviewClose');
    const items = list.querySelectorAll('.tp-item');

    // Set initial selected template
    const initialId = hiddenInput.value;
    if (initialId) {
        items.forEach(function(item) {
            if (item.dataset.id === initialId) {
                selectItem(item, false);
            }
        });
    }

    // Toggle dropdown
    selected.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = picker.classList.contains('open');
        closeAllPickers();
        if (!isOpen) {
            picker.classList.add('open');
            setTimeout(function() { searchInput.focus(); }, 50);
        }
    });

    // Search filter
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        items.forEach(function(item) {
            const name = (item.dataset.name || '').toLowerCase();
            if (!query || name.indexOf(query) !== -1) {
                item.classList.remove('tp-item-hidden');
            } else {
                item.classList.add('tp-item-hidden');
            }
        });
    });

    // Prevent dropdown close when clicking search
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Item click
    items.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            selectItem(item, true);
            picker.classList.remove('open');
            searchInput.value = '';
            items.forEach(function(i) { i.classList.remove('tp-item-hidden'); });
        });
    });

    // Select item
    function selectItem(item, showPreview) {
        const id = item.dataset.id;
        const name = item.dataset.name;
        const contentB64 = item.dataset.content;

        hiddenInput.value = id;
        selectedName.textContent = name;

        // Update active state
        items.forEach(function(i) { i.classList.remove('active'); });
        item.classList.add('active');

        // Update selected thumbnail
        const thumbIframe = item.querySelector('.tp-thumb-iframe');
        if (thumbIframe && id) {
            selectedThumb.innerHTML = '';
            const miniIframe = document.createElement('iframe');
            miniIframe.srcdoc = thumbIframe.srcdoc;
            miniIframe.sandbox = '';
            miniIframe.scrolling = 'no';
            miniIframe.tabIndex = -1;
            miniIframe.style.cssText = 'width:400px;height:300px;transform:scale(0.1);transform-origin:0 0;pointer-events:none;border:none;';
            selectedThumb.appendChild(miniIframe);
        } else {
            selectedThumb.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>';
        }

        // Show/hide preview
        if (id && contentB64 && showPreview) {
            try {
                const content = decodeB64UTF8(contentB64);
                previewIframe.srcdoc = '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' + content + '</body></html>';
                previewWrap.style.display = 'block';
            } catch(e) {
                previewWrap.style.display = 'none';
            }
        } else if (!id) {
            previewWrap.style.display = 'none';
        }
    }

    // Close on outside click
    document.addEventListener('click', function() {
        closeAllPickers();
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllPickers();
        }
    });

    // Preview close button
    previewClose.addEventListener('click', function() {
        previewWrap.style.display = 'none';
    });

    // UTF-8 safe base64 decode
    function decodeB64UTF8(str) {
        const binStr = atob(str);
        const bytes = new Uint8Array(binStr.length);
        for (let i = 0; i < binStr.length; i++) {
            bytes[i] = binStr.charCodeAt(i);
        }
        return new TextDecoder('utf-8').decode(bytes);
    }

    function closeAllPickers() {
        document.querySelectorAll('.tp-picker.open').forEach(function(p) {
            p.classList.remove('open');
        });
    }
});
</script>
@endpush
