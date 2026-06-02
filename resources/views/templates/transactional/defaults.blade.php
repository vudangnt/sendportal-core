@extends('sendportal::layouts.app')

@section('heading'){{ __('Default Transactional Templates') }}@endsection

@section('content')
<div class="container-fluid">
    <a href="{{ url('/templates#transactional') }}" class="btn btn-light btn-sm mb-2">
        <i class="fas fa-arrow-left"></i> {{ __('Back to Templates') }}
    </a>
    <h3>Defaults available to clone</h3>
    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead><tr><th>Code</th><th>Name</th><th>Subject</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($defaults as $d)
                <tr>
                    <td><code>{{ $d->code }}</code></td>
                    <td>{{ $d->name }}</td>
                    <td class="text-muted">{{ Str::limit($d->subject, 70) }}</td>
                    <td>
                        @if($cloned->has($d->code))
                            <a href="{{ route('sendportal.templates.transactional.edit', $cloned[$d->code]) }}"
                               class="btn btn-sm btn-info">Edit your version</a>
                        @else
                            <form action="{{ route('sendportal.templates.transactional.clone', $d->code) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-primary">Clone into workspace</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No defaults exist.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
