{{-- Transactional templates grid (workspace-scoped) --}}
<div class="row">
    @forelse($transactionalTemplates as $template)
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 template-card" data-name="{{ $template->name }}">
            <div class="card h-100 shadow-sm template-item">
                {{-- Header strip with code badge --}}
                <div class="px-3 pt-2 d-flex justify-content-between align-items-center">
                    <code class="text-info" style="font-size: 12px;">{{ $template->code }}</code>
                    <span class="badge badge-info" style="font-size: 10px;">TRANSACTIONAL</span>
                </div>

                {{-- Preview --}}
                <a href="{{ route('sendportal.templates.transactional.edit', $template->id) }}"
                   class="d-block template-preview-link">
                    <div class="template-preview-wrapper">
                        @if($template->content)
                            <iframe width="100%" height="220" scrolling="no" frameborder="0"
                                    srcdoc="{{ $template->content }}" class="template-iframe"></iframe>
                        @else
                            <div class="d-flex h-100 align-items-center justify-content-center text-muted">
                                <em>{{ __('No content') }}</em>
                            </div>
                        @endif
                        <div class="template-overlay">
                            <span class="btn btn-light btn-sm"><i class="fa fa-edit mr-1"></i>{{ __('Edit Template') }}</span>
                        </div>
                    </div>
                </a>

                {{-- Info --}}
                <div class="card-body py-2 px-3">
                    <h6 class="card-title mb-1 text-truncate" title="{{ $template->name }}">{{ $template->name }}</h6>
                    <small class="text-muted" title="{{ $template->subject }}">
                        <i class="fas fa-envelope-open mr-1"></i>
                        {{ \Illuminate\Support\Str::limit($template->subject ?? '—', 45) }}
                    </small>
                </div>

                {{-- Actions --}}
                <div class="card-footer bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('sendportal.templates.transactional.edit', $template->id) }}"
                       class="btn btn-xs btn-outline-primary">
                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <form action="{{ route('sendportal.templates.transactional.destroy', $template->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this template?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline-danger">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('No transactional templates yet') }}</h5>
                    <p class="text-muted">{{ __('Workspaces get the six default transactional templates auto-seeded — if you see none, browse defaults to clone.') }}</p>
                    <a href="{{ route('sendportal.templates.transactional.defaults') }}" class="btn btn-primary">
                        <i class="fa fa-list mr-1"></i> {{ __('Browse defaults') }}
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{ $transactionalTemplates->links() }}
