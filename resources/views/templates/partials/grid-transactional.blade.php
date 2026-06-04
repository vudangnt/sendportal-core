{{-- Transactional templates grid (workspace-scoped) — compact cards --}}
<style>
    .tx-card {
        transition: transform .15s ease, box-shadow .15s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    .tx-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(20,40,90,.12) !important;
    }
    .tx-code-banner {
        background: linear-gradient(135deg, #f6f9ff 0%, #eef4ff 100%);
        border-bottom: 1px solid #e5edfa;
        padding: 6px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
    }
    .tx-code-pill {
        display: inline-flex;
        align-items: baseline;
        gap: 5px;
        padding: 2px 8px;
        background: #fff;
        border: 1px solid #c8d8f7;
        border-radius: 100px;
        font-size: 11px;
        line-height: 1.3;
        color: #1a4fc7;
        max-width: 100%;
        overflow: hidden;
    }
    .tx-code-pill .tx-code-label {
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #6b7a99;
        font-size: 9px;
    }
    .tx-code-pill code {
        background: transparent;
        color: #1a4fc7;
        font-weight: 600;
        padding: 0;
        font-size: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tx-kind-tag {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .08em;
        padding: 2px 6px;
        background: #2c6df0;
        color: #fff;
        border-radius: 3px;
        flex-shrink: 0;
    }
    .tx-preview-wrap {
        position: relative;
        overflow: hidden;
        height: 130px;
        background: #fafbfc;
    }
    .tx-preview-iframe {
        pointer-events: none;
        transform: scale(0.4);
        transform-origin: top left;
        width: 250%;
        height: 325%;
        border: 0;
    }
    .tx-preview-empty {
        display: flex; align-items: center; justify-content: center;
        height: 100%; color: #c0c8d4; font-size: 11px; font-style: italic;
    }
    .tx-preview-overlay {
        position: absolute; inset: 0;
        background: rgba(0,0,0,0.35);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity .15s ease;
    }
    .tx-card:hover .tx-preview-overlay { opacity: 1; }
    .tx-meta {
        padding: 8px 10px;
        font-size: 12px;
        line-height: 1.3;
    }
    .tx-meta .tx-name {
        font-weight: 600;
        color: #2d3748;
        margin: 0 0 2px;
        font-size: 12.5px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tx-meta .tx-subject {
        color: #8a94a6;
        font-size: 11px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
    }
    .tx-actions {
        padding: 6px 10px;
        background: #fff;
        border-top: 1px solid #f0f2f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .tx-actions .btn { font-size: 11px; padding: 2px 8px; }
</style>

<div class="row">
    @forelse($transactionalTemplates as $template)
        @php($status = $template->source_status ?? 'custom')
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12 mb-3 template-card" data-name="{{ $template->name }}">
            <div class="card h-100 shadow-sm tx-card">
                <div class="tx-code-banner">
                    <span class="tx-code-pill" title="{{ __('Template code') }}: {{ $template->code }}">
                        <span class="tx-code-label">{{ __('Code') }}</span>
                        <code>{{ $template->code ?? '—' }}</code>
                    </span>
                    @if($status === 'inherited')
                        <span class="badge badge-secondary" style="font-size:9px;">{{ __('Default') }}</span>
                    @elseif($status === 'customized')
                        <span class="badge badge-success" style="font-size:9px;">{{ __('Customized') }}</span>
                    @else
                        <span class="badge" style="font-size:9px;background:#7c3aed;color:#fff;">{{ __('Custom') }}</span>
                    @endif
                </div>

                <div class="tx-preview-wrap">
                    @if($template->content)
                        <iframe scrolling="no" srcdoc="{{ $template->content }}" class="tx-preview-iframe"></iframe>
                    @else
                        <div class="tx-preview-empty">{{ __('No content') }}</div>
                    @endif
                </div>

                <div class="tx-meta">
                    <div class="tx-name" title="{{ $template->name }}">{{ $template->name }}</div>
                    <span class="tx-subject" title="{{ $template->subject }}">
                        {{ \Illuminate\Support\Str::limit($template->subject ?? '—', 50) }}
                    </span>
                </div>

                <div class="tx-actions">
                    @if($status === 'inherited')
                        <span class="text-muted" style="font-size:11px;">{{ __('Inherited from default') }}</span>
                        <form action="{{ route('sendportal.templates.transactional.clone', ['code' => $template->code]) }}"
                              method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-pen"></i> {{ __('Customize') }}
                            </button>
                        </form>
                    @elseif($status === 'customized')
                        <a href="{{ route('sendportal.templates.transactional.edit', $template->id) }}"
                           class="btn btn-outline-primary">
                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <form action="{{ route('sendportal.templates.transactional.destroy', $template->id) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('{{ __('Reset to the super-admin default? Your customization will be removed.') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-secondary" title="{{ __('Reset to default') }}">
                                <i class="fa fa-undo"></i> {{ __('Reset') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('sendportal.templates.transactional.edit', $template->id) }}"
                           class="btn btn-outline-primary">
                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <form action="{{ route('sendportal.templates.transactional.destroy', $template->id) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('{{ __('Are you sure you want to delete this template?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('No transactional templates') }}</h5>
                    <p class="text-muted">{{ __('No default templates are configured yet.') }}</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($transactionalTemplates instanceof \Illuminate\Contracts\Pagination\Paginator && $transactionalTemplates->hasPages())
    {{ $transactionalTemplates->links() }}
@endif
