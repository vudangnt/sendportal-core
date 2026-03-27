@extends('sendportal::layouts.app')

@section('title', __('Industries'))

@section('heading')
    {{ __('Industries') }}
@endsection

@section('content')
    @component('sendportal::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.industries.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Industry') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-industry mr-1"></i> {{ __('Industries') }}</span>
            <input type="text" id="search-input" class="form-control form-control-sm" placeholder="{{ __('Search industries...') }}" style="width: 250px;">
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush" id="item-tree">
                @forelse($industries as $industry)
                    <li class="list-group-item item-row">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                @if(isset($industry['children']) && count($industry['children']) > 0)
                                    <a href="javascript:void(0)" class="mr-2 toggle-children" data-target="industry-{{ $industry['id'] }}">
                                        <i class="fas fa-chevron-right toggle-icon"></i>
                                    </a>
                                @else
                                    <span class="mr-2" style="width: 16px; display: inline-block;"></span>
                                @endif
                                <span class="item-name font-weight-bold">{{ $industry['name'] }}</span>
                                <span class="badge badge-warning badge-pill ml-2">{{ $industry['active_subscribers_count'] ?? 0 }} {{ __('subscribers') }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('sendportal.industries.edit', $industry['id']) }}" class="btn btn-sm btn-outline-secondary mr-1" title="{{ __('Edit') }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('sendportal.industries.destroy', $industry['id']) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure you want to delete this industry?') }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if(isset($industry['children']) && count($industry['children']) > 0)
                            <ul class="list-group mt-2 ml-4 children-list" id="industry-{{ $industry['id'] }}" style="display: none;">
                                @foreach($industry['children'] as $child)
                                    <li class="list-group-item item-row">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="item-name">{{ $child['name'] }}</span>
                                                <span class="badge badge-light badge-pill ml-2">{{ $child['active_subscribers_count'] ?? 0 }} {{ __('subscribers') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('sendportal.industries.edit', $child['id']) }}" class="btn btn-sm btn-outline-secondary mr-1" title="{{ __('Edit') }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('sendportal.industries.destroy', $child['id']) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">
                        <i class="fas fa-industry fa-2x mb-2 d-block"></i>
                        {{ __('No industries found. Create your first industry!') }}
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            $('.toggle-children').click(function() {
                var target = $('#' + $(this).data('target'));
                target.slideToggle(200);
                $(this).find('.toggle-icon').toggleClass('fa-chevron-right fa-chevron-down');
            });

            $('#search-input').on('input', function() {
                var search = $(this).val().toLowerCase().trim();
                $('#item-tree > .item-row').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(search === '' || text.indexOf(search) !== -1);
                });
            });
        });
    </script>
@endpush
