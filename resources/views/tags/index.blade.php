@extends('sendportal::layouts.app')

@section('title', __('Tags'))

@section('heading')
    {{ __('Tags') }}
@endsection

@section('content')
    @component('sendportal::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.tags.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Tag') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-tags mr-1"></i> {{ __('Tags') }}</span>
            <input type="text" id="search-input" class="form-control form-control-sm" placeholder="{{ __('Search tags...') }}" style="width: 250px;">
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush" id="item-tree">
                @forelse($tags as $tag)
                    <li class="list-group-item item-row">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                @if(isset($tag['children']) && count($tag['children']) > 0)
                                    <a href="javascript:void(0)" class="mr-2 toggle-children" data-target="tag-{{ $tag['id'] }}">
                                        <i class="fas fa-chevron-right toggle-icon"></i>
                                    </a>
                                @else
                                    <span class="mr-2" style="width: 16px; display: inline-block;"></span>
                                @endif
                                <span class="item-name font-weight-bold">{{ $tag['name'] }}</span>
                                <span class="badge badge-secondary badge-pill ml-2">{{ $tag['active_subscribers_count'] }} {{ __('subscribers') }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('sendportal.tags.edit', $tag['id']) }}" class="btn btn-sm btn-outline-secondary mr-1" title="{{ __('Edit') }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('sendportal.tags.destroy', $tag['id']) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure you want to delete this tag?') }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if(isset($tag['children']) && count($tag['children']) > 0)
                            <ul class="list-group mt-2 ml-4 children-list" id="tag-{{ $tag['id'] }}" style="display: none;">
                                @foreach($tag['children'] as $childTag)
                                    <li class="list-group-item item-row">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="item-name">{{ $childTag['name'] }}</span>
                                                <span class="badge badge-light badge-pill ml-2">{{ $childTag['active_subscribers_count'] }} {{ __('subscribers') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('sendportal.tags.edit', $childTag['id']) }}" class="btn btn-sm btn-outline-secondary mr-1" title="{{ __('Edit') }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('sendportal.tags.destroy', $childTag['id']) }}" method="POST" class="d-inline delete-form">
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
                        <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                        {{ __('No tags found. Create your first tag!') }}
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            // Toggle children
            $('.toggle-children').click(function() {
                var target = $('#' + $(this).data('target'));
                target.slideToggle(200);
                $(this).find('.toggle-icon').toggleClass('fa-chevron-right fa-chevron-down');
            });

            // Search filter
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
