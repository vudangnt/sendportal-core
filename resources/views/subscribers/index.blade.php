@extends('sendportal::layouts.app')

@section('title', __('Subscribers'))

@section('heading')
    {{ __('Subscribers') }}
@endsection

@section('content')

    @component('sendportal::layouts.partials.actions')

        @slot('left')
            <form action="{{ route('sendportal.subscribers.index') }}" method="GET" class="form-inline mb-3 mb-md-0">
                <input class="form-control form-control-sm" name="name" type="text" value="{{ request('name') }}"
                       placeholder="{{ __('Search...') }}">

                <div class="mr-2">
                    <select name="status" class="form-control form-control-sm">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option
                            value="subscribed" {{ request('status') == 'subscribed' ? 'selected' : '' }}>{{ __('Subscribed') }}</option>
                        <option
                            value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>{{ __('Unsubscribed') }}</option>

                            <option
                            value="no_tags" {{ request('status') == 'no_tags' ? 'selected' : '' }}>{{ __('Not has tags') }}</option>
                            <option
                            value="no_locations" {{ request('status') == 'no_locations' ? 'selected' : '' }}>{{ __('Not has locations') }}</option>
                    </select>
                </div>

                @if(count($tags))
                    <div id="tagFilterSelector" class="mr-2">
                        <select multiple="" class="selectpicker form-control form-control-sm" name="tags[]" data-width="auto">
                            @foreach($tags as $tagId => $tagName)
                                <option value="{{ $tagId }}" @if(in_array($tagId, request()->get('tags') ?? [])) selected @endif>{{ $tagName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if(count($locations))
                    <div id="tagFilterSelector" class="mr-2">
                        <select multiple="" class="selectpicker form-control form-control-sm" name="locations[]" data-width="auto" >
                        @foreach($locations as $locationId => $locationName)
                                <option value="{{ $locationId }}" @if(in_array($locationId, request()->get('locations') ?? [])) selected @endif>{{ $locationName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <button type="submit" class="btn btn-light btn-md">{{ __('Search') }}</button>

                @if(request()->anyFilled(['name', 'status']))
                    <a href="{{ route('sendportal.subscribers.index') }}"
                       class="btn btn-md btn-light">{{ __('Clear') }}</a>
                @endif
            </form>
        @endslot

        @slot('right')
            <div class="btn-group mr-2">
                <button id="deleteSelectedBtn" class="btn btn-danger btn-md" style="display: none;">
                    <i class="fa fa-trash mr-1"></i> {{ __('Delete Selected') }}
                </button>
                <button class="btn btn-md btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-bars color-gray-400"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('sendportal.subscribers.import') }}" class="dropdown-item">
                        <i class="fa fa-upload color-gray-400 mr-2"></i> {{ __('Import Subscribers') }}
                    </a>
                    <a href="{{ route('sendportal.subscribers.export') }}" class="dropdown-item">
                        <i class="fa fa-download color-gray-400 mr-2"></i> {{ __('Export Subscribers') }}
                    </a>

                </div>
            </div>
            <a class="btn btn-light btn-md mr-2" href="{{ route('sendportal.tags.index') }}">
                <i class="fa fa-tag color-gray-400 mr-1"></i> {{ __('Tags') }}
            </a>

            <a class="btn btn-light btn-md mr-2" href="{{ route('sendportal.locations.index') }}">
                <i class="fa fa-location-arrow color-gray-400 mr-1"></i> {{ __('Locations') }}
            </a>
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.subscribers.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Subscriber') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll" class="select-all-checkbox">
                    </th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Tags') }}</th>
                    <th>{{ __('Locations') }}</th>
                    <th>{{ __('Updated') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subscribers as $subscriber)
                    <tr>
                        <td>
                            <input type="checkbox" class="subscriber-checkbox" value="{{ $subscriber->id }}">
                        </td>
                        <td>
                            <a href="{{ route('sendportal.subscribers.show', $subscriber->id) }}">
                                {{ $subscriber->email }}
                            </a>
                        </td>
                        <td>{{ $subscriber->full_name }}</td>
                        <td>
                            @forelse($subscriber->tags as $tag)
                                <span class="badge badge-light">{{ $tag->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td>
                            @forelse($subscriber->locations as $location)
                                <span class="badge badge-light">{{ $location->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td><span
                                title="{{ $subscriber->updated_at }}">{{ $subscriber->updated_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            @if($subscriber->unsubscribed_at)
                                <span class="badge badge-danger">{{ __('Unsubscribed') }}</span>
                            @else
                                <span class="badge badge-success">{{ __('Subscribed') }}</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('sendportal.subscribers.destroy', $subscriber->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a href="{{ route('sendportal.subscribers.edit', $subscriber->id) }}"
                                   class="btn btn-xs btn-light">{{ __('Edit') }}</a>
                                <button type="submit"
                                        class="btn btn-xs btn-light delete-subscriber">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            <p class="empty-table-text">{{ __('No Subscribers Found') }}</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('sendportal::layouts.partials.pagination', ['records' => $subscribers])

    <script>
        let subscribers = document.getElementsByClassName('delete-subscriber');

        Array.from(subscribers).forEach((element) => {
            element.addEventListener('click', (event) => {
                event.preventDefault();

                let confirmDelete = confirm('Are you sure you want to permanently delete this subscriber and all associated data?');

                if (confirmDelete) {
                    element.closest('form').submit();
                }
            });
        });
    </script>

@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/css/bootstrap-select.min.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chỉ chạy nếu có thông báo import đang xử lý
        if (document.querySelector('.import-processing')) {
            function checkProgress() {
                fetch('/api/v1/import-processing')
                    .then(response => response.json())
                    .then(data => {
                        // Cập nhật thanh tiến trình
                        updateProgressBar(data);

                        // Kiểm tra nếu đã hoàn thành tất cả chunks
                        if (data.completed_chunks && data.total_chunks && 
                            data.completed_chunks >= data.total_chunks) {
                            // Import hoàn tất
                            showCompletedMessage();
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            // Kiểm tra lại sau 2 giây
                            setTimeout(checkProgress, 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorMessage(error);
                    });
            }

            function updateProgressBar(data) {
                const progressBar = document.querySelector('.progress-bar');
                const statusText = document.querySelector('.import-status');
                const timeRemaining = document.querySelector('.time-remaining');
                
                if (progressBar) {
                    const progress = Math.round(data.progress);
                    progressBar.style.width = progress + '%';
                    progressBar.setAttribute('aria-valuenow', progress);
                    
                    // Cập nhật thông tin chunks
                    statusText.textContent = `Đã xử lý ${data.completed_chunks}/${data.total_chunks} chunks (${progress}%)`;
                    
                    // Hiển thị thời gian còn lại
                    if (data.estimated_time) {
                        timeRemaining.textContent = `Thời gian còn lại: ${formatTime(data.estimated_time)}`;
                    }

                    // Thay đổi màu dựa trên tiến trình
                    if (progress < 25) {
                        progressBar.classList.remove('bg-success', 'bg-warning');
                        progressBar.classList.add('bg-info');
                    } else if (progress < 75) {
                        progressBar.classList.remove('bg-info', 'bg-success');
                        progressBar.classList.add('bg-warning');
                    } else {
                        progressBar.classList.remove('bg-warning', 'bg-info');
                        progressBar.classList.add('bg-success');
                    }
                }
            }

            function formatTime(seconds) {
                if (seconds < 60) return `${seconds} giây`;
                if (seconds < 3600) {
                    const minutes = Math.floor(seconds / 60);
                    const remainingSeconds = seconds % 60;
                    return `${minutes} phút ${remainingSeconds} giây`;
                }
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                return `${hours} giờ ${minutes} phút`;
            }

            function showCompletedMessage() {
                const alert = document.querySelector('.import-processing');
                alert.classList.remove('alert-info');
                alert.classList.add('alert-success');
                alert.innerHTML = `
                    <strong>Import hoàn tất!</strong><br>
                    Trang sẽ tự động tải lại sau 2 giây...
                `;
            }

            function showErrorMessage(error) {
                const alert = document.querySelector('.import-processing');
                alert.classList.remove('alert-info');
                alert.classList.add('alert-danger');
                alert.innerHTML = `
                    <strong>Có lỗi xảy ra!</strong><br>
                    ${error.message}
                `;
            }

            // Bắt đầu kiểm tra tiến trình
            checkProgress();
        }

        const selectAllCheckbox = document.getElementById('selectAll');
        const subscriberCheckboxes = document.getElementsByClassName('subscriber-checkbox');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

        // Xử lý checkbox "Select All"
        selectAllCheckbox.addEventListener('change', function() {
            Array.from(subscriberCheckboxes).forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });

        // Xử lý các checkbox riêng lẻ
        Array.from(subscriberCheckboxes).forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateDeleteButton();
                // Cập nhật trạng thái "Select All"
                selectAllCheckbox.checked = Array.from(subscriberCheckboxes).every(cb => cb.checked);
            });
        });

        // Hiển thị/ẩn nút Delete Selected
        function updateDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
            deleteSelectedBtn.style.display = checkedBoxes.length > 0 ? 'inline-block' : 'none';
        }

        // Xử lý xóa các subscriber đã chọn
        deleteSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
            const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

            if (selectedIds.length === 0) return;

            if (confirm('Bạn có chắc chắn muốn xóa ' + selectedIds.length + ' subscriber đã chọn?')) {
                // Tạo form và submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("sendportal.subscribers.destroy-all") }}';
                
                // Thêm CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Thêm method spoofing
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                // Thêm IDs
                const idsField = document.createElement('input');
                idsField.type = 'hidden';
                idsField.name = 'ids';
                idsField.value = JSON.stringify(selectedIds);
                form.appendChild(idsField);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    </script>
@endpush

@if (session('success') && str_contains(session('success'), 'Import đang được xử lý'))
<div class="alert alert-info import-processing">
    <h5 class="alert-heading">{{ session('success') }}</h5>
    <div class="import-status mb-2">Đang bắt đầu import...</div>
    <div class="time-remaining mb-2">Đang tính toán thời gian...</div>
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" 
             style="width: 0%" 
             aria-valuenow="0" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>
    <small class="mt-2 text-muted">Vui lòng không đóng trang trong quá trình import</small>
</div>
@endif
