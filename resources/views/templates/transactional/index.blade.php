@extends('sendportal::layouts.app')

@section('heading'){{ __('Transactional Templates') }}@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col"><h3>Transactional Templates</h3></div>
        <div class="col-auto">
            <a href="{{ route('sendportal.templates.transactional.defaults') }}" class="btn btn-light">
                <i class="fas fa-list"></i> Browse defaults
            </a>
            <a href="{{ route('sendportal.templates.transactional.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th><th>Name</th><th>Source</th>
                        <th>Last edit</th><th style="width:200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $t)
                    <tr>
                        <td><code>{{ $t->code }}</code></td>
                        <td>{{ $t->name }}</td>
                        <td>
                            @if($t->source === 'seeded')
                                <span class="badge badge-light">Seeded</span>
                            @elseif($t->source === 'modified')
                                <span class="badge badge-info">&#9998; Modified</span>
                            @else
                                <span class="badge badge-warning">&#11088; Custom code</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $t->updated_at?->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('sendportal.templates.transactional.edit', $t->id) }}"
                               class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('sendportal.templates.transactional.destroy', $t->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete template {{ $t->code }}?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No transactional templates yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
