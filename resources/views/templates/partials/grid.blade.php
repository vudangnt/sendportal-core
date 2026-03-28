<div class="row">
    @forelse($templates as $template)
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 template-card" data-name="{{ $template->name }}">
            <div class="card h-100 shadow-sm template-item">
                {{-- Preview --}}
                <a href="{{ route('sendportal.templates.edit', $template->id) }}" class="d-block template-preview-link">
                    <div class="template-preview-wrapper">
                        <iframe width="100%" height="280" scrolling="no" frameborder="0"
                                srcdoc="{{ $template->content }}" class="template-iframe"></iframe>
                        <div class="template-overlay">
                            <span class="btn btn-light btn-sm"><i class="fa fa-edit mr-1"></i>{{ __('Edit Template') }}</span>
                        </div>
                    </div>
                </a>

                {{-- Info --}}
                <div class="card-body py-2 px-3">
                    <h6 class="card-title mb-1 text-truncate" title="{{ $template->name }}">{{ $template->name }}</h6>
                    <small class="text-muted">
                        <i class="far fa-calendar-alt mr-1"></i>{{ $template->created_at ? $template->created_at->format('d M Y') : '' }}
                        @if($template->is_in_use)
                            <span class="badge badge-success badge-pill ml-1">{{ __('In use') }}</span>
                        @endif
                    </small>
                </div>

                {{-- Actions --}}
                <div class="card-footer bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('sendportal.templates.edit', $template->id) }}" class="btn btn-xs btn-outline-primary" title="{{ __('Edit') }}">
                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <a href="{{ route('sendportal.templates.duplicate', $template->id) }}" class="btn btn-xs btn-outline-secondary" title="{{ __('Duplicate') }}">
                            <i class="fa fa-copy"></i>
                        </a>
                        <a href="{{ route('sendportal.templates.export', $template->id) }}" class="btn btn-xs btn-outline-secondary" title="{{ __('Export') }}">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                    @if(!$template->is_in_use)
                        <form action="{{ route('sendportal.templates.destroy', $template->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger" title="{{ __('Delete') }}"
                                    onclick="return confirm('{{ __('Are you sure you want to delete this template?') }}')">
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
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('No templates yet') }}</h5>
                    <p class="text-muted">{{ __('Create your first email template to get started.') }}</p>
                    <a href="{{ route('sendportal.templates.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-1"></i> {{ __('New Template') }}
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{ $templates->links() }}

@push('css')
<style>
    .template-preview-wrapper {
        position: relative;
        overflow: hidden;
        height: 280px;
        background: #f8f9fa;
    }
    .template-iframe {
        pointer-events: none;
        transform: scale(0.5);
        transform-origin: top left;
        width: 200%;
        height: 560px;
    }
    .template-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .template-preview-link:hover .template-overlay {
        opacity: 1;
    }
    .template-item {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .template-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
    }
</style>
@endpush
