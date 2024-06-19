@extends('sendportal::layouts.app')

@section('title', __('Locations'))

@section('heading')
    {{ __('Locations') }}
@endsection

@section('content')
    @component('sendportal::layouts.partials.actions')

        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.locations.create') }}">
                <i class="fa fa-plus"></i> {{ __('New Location') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th>{{ __('Tupe') }}</th>
                    <th>{{ __('Subscribers') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td>
                            <a href="{{ route('sendportal.locations.edit', $location['id']) }}">
                                {{ $location['name'] }}
                            </a>
                        </td>
                        <td>{{ $location['code'] }}</td>

                        <td>{{ $location['type'] }}</td>

                        <td>{{ $location['subscribers_count'] }}</td>
                        <td>
                            @include('sendportal::locations.partials.actions')
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            <p class="empty-table-text">{{ __('You have not created any locations.') }}</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{--    @include('sendportal::layouts.partials.pagination', ['records' => $locations])--}}

@endsection
