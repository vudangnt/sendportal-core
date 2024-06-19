@extends('sendportal::layouts.app')

@section('title', __('Tags'))

@section('heading')
    {{ __('Tags') }}
@endsection

@section('content')
    @component('sendportal::layouts.partials.actions')

        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.tags.create') }}">
                <i class="fa fa-plus"></i> {{ __('New Tag') }}
            </a>
        @endslot
    @endcomponent

    <h2>List Tags</h2>
    <ul id="tag-tree">
        @foreach($tags as $tag)
            <li>
                <div class="actions form-group" style="margin-left: auto;">
                    <a class="link"> <span class="toggle"
                                           onclick="toggleElementById({{ "tag".$tag['id']}})">{{ $tag['name'] }} ({{ $tag['active_subscribers_count'] }} {{ __('subscribers') }} )</span></a>
                    <a class="pl-3" href="{{ route('sendportal.tags.edit', $tag['id']) }}"
                       >
                        <i class="fa fa-edit" aria-hidden="false"></i>
                    </a>
                    <form class="pl-3" action="{{ route('sendportal.tags.destroy', $tag['id']) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="fa fa-trash" style="color: red; border:none "></button>
                    </form>
                </div>

                @if( isset($tag['children']) && count($tag['children']??0) > 0)
                    <ul style="display: block;" id="tag{{$tag['id']}}">
                        @include('sendportal::tags.partials.subtags', ['subtags' => $tag['children']])
                    </ul>
                @endif

            </li>
        @endforeach
    </ul>

    <style>
        .form-group {
            margin: 0 auto 1rem auto;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
    </style>

    <script>
        function toggleElementById(elementId) {
            console.log(elementId);
            var element = document.getElementById(elementId);
            if (elementId) {
                var isHidden = elementId.style.display === 'none';
                elementId.style.display = isHidden ? 'block' : 'none';
            }
        }

        function toggleChildren(element) {
            var nextUl = element.parentNode.nextElementSibling.querySelector('ul');
            console.log(nextUl);
            if (nextUl) {
                var isHidden = nextUl.style.display === 'none';
                nextUl.style.display = isHidden ? 'block' : 'none';
            }
        }
    </script>
@endsection
