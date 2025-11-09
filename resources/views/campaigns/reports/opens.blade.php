@extends('sendportal::layouts.app')

@section('title', $campaign->name)

@section('heading', $campaign->name)


@section('content')

    @include('sendportal::campaigns.reports.partials.nav')

    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-md-0 mb-3">
            <div class="widget flex-row align-items-center align-items-stretch">
                <div class="col-8 py-4 rounded-right">
                    <div class="h4 m-0">{{ $campaign->unique_open_count }}</div>
                    <div class="text-uppercase">{{ __('Unique Opens') }}</div>
                </div>
                <div class="col-4 d-flex align-items-center justify-content-center rounded-left">
                    <em class="far fa-envelope-open fa-2x color-gray-400"></em>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-md-0 mb-3">
            <div class="widget flex-row align-items-center align-items-stretch">
                <div class="col-8 py-4 rounded-right">
                    <div class="h4 m-0">{{ $campaign->total_open_count }}</div>
                    <div class="text-uppercase">{{ __('Total Opens') }}</div>
                </div>
                <div class="col-4 d-flex align-items-center justify-content-center rounded-left">
                    <em class="fas fa-envelope-open fa-2x color-gray-400"></em>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-md-0 mb-3">
            <div class="widget flex-row align-items-center align-items-stretch">
                <div class="col-8 py-4 rounded-right">
                    <div class="h4 m-0">{{ $averageTimeToOpen }}</div>
                    <div class="text-uppercase">{{ __('Avg. Time To Open') }}</div>
                </div>
                <div class="col-4 d-flex align-items-center justify-content-center rounded-left">
                    <em class="far fa-clock fa-2x color-gray-400"></em>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form id="opens-bulk-form" method="POST" action="{{ route('sendportal.campaigns.reports.bulk-tag', $campaign->id) }}">
                @csrf
                <input type="hidden" name="context" value="opens">
                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                <input type="hidden" name="tag_label" id="opens-tag-label">

                <div class="card">
                    <div class="card-body pb-0">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mb-3">
                            <h5 class="mb-2 mb-md-0">{{ __('Danh sách mở mail') }}</h5>
                            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center">
                                <button type="button" class="btn btn-primary btn-sm mb-2 mb-md-0" id="opens-bulk-tag-button" disabled>
                                    {{ __('Thêm vào Tag mới') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-table table-responsive">
                        @php
                            $currentSort = request()->get('sort', 'opened_at');
                            $currentDirection = request()->get('direction', 'asc');
                            $openCountDirection = $currentSort === 'open_count' && $currentDirection === 'asc' ? 'desc' : 'asc';
                            $openCountQuery = array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'open_count', 'direction' => $openCountDirection]);
                        @endphp
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="opens-select-all">
                                </th>
                                <th>{{ __('Subscriber') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Opened') }}</th>
                                <th>
                                    <a href="{{ route('sendportal.campaigns.reports.opens', array_merge(['id' => $campaign->id], $openCountQuery)) }}" class="text-reset text-decoration-none">
                                        {{ __('Open Count') }}
                                        @if($currentSort === 'open_count')
                                            <em class="fas fa-sort-amount-{{ $currentDirection === 'asc' ? 'up' : 'down' }} ml-1"></em>
                                        @else
                                            <em class="fas fa-sort ml-1 text-muted"></em>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($messages as $message)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" class="opens-message-checkbox">
                                    </td>
                                    <td>
                                        <a href="{{ route('sendportal.subscribers.show', $message->subscriber_id) }}">{{ $message->recipient_email }}</a>
                                    </td>
                                    <td>{{ $message->subject }}</td>
                                    <td>{{ \Sendportal\Base\Facades\Helper::displayDate($message->opened_at) }}</td>
                                    <td>{{ $message->open_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="empty-table-text">{{ __('There are no messages') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </form>
        </div>
    </div>


    @include('sendportal::layouts.partials.pagination', ['records' => $messages])

<div class="modal fade" id="opens-bulk-tag-modal" tabindex="-1" role="dialog" aria-labelledby="opens-bulk-tag-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="opens-bulk-tag-modal-label">{{ __('Thêm tag cho các subscriber đã chọn') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="opens-tag-input">{{ __('Nhập nhãn tag') }}</label>
                    <input type="text" class="form-control" id="opens-tag-input" placeholder="{{ __('Ví dụ: khach_hang_than_thiet') }}" maxlength="191" required>
                    <small class="form-text text-muted">{{ __('Tag sẽ được tạo với prefix cố định "re_mkt_"') }}</small>
                    <div class="invalid-feedback">
                        {{ __('Vui lòng nhập nhãn tag hợp lệ.') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Hủy') }}</button>
                <button type="button" class="btn btn-primary" id="opens-bulk-tag-submit">{{ __('Xác nhận') }}</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(function () {
            var selectAll = $('#opens-select-all');
            var checkboxes = $('.opens-message-checkbox');
            var bulkButton = $('#opens-bulk-tag-button');
            var modal = $('#opens-bulk-tag-modal');
            var form = $('#opens-bulk-form');
            var tagInput = $('#opens-tag-input');
            var tagLabelInput = $('#opens-tag-label');

            function updateBulkButtonState() {
                var anyChecked = checkboxes.filter(':checked').length > 0;
                bulkButton.prop('disabled', !anyChecked);
            }

            selectAll.on('change', function () {
                checkboxes.prop('checked', this.checked);
                updateBulkButtonState();
            });

            form.on('change', '.opens-message-checkbox', function () {
                if (!this.checked) {
                    selectAll.prop('checked', false);
                } else if (checkboxes.length === checkboxes.filter(':checked').length) {
                    selectAll.prop('checked', true);
                }
                updateBulkButtonState();
            });

            bulkButton.on('click', function () {
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function () {
                tagInput.removeClass('is-invalid');
                tagInput.val('');
            });

            $('#opens-bulk-tag-submit').on('click', function () {
                var value = $.trim(tagInput.val());

                if (!value.length) {
                    tagInput.addClass('is-invalid');
                    return;
                }

                tagInput.removeClass('is-invalid');
                tagLabelInput.val(value);
                modal.modal('hide');
                form.submit();
            });
        });
    </script>
@endpush

@endsection
