@extends('sendportal::layouts.app')

@section('title', __('Subscribers'))

@section('heading')
    {{ __('Subscribers') }}
@endsection

@section('content')

    @component('sendportal::layouts.partials.actions')

        @slot('left')
            <form action="{{ route('sendportal.subscribers.index') }}" method="GET" class="form-inline mb-3 mb-md-0 flex-wrap">
                <input class="form-control form-control-sm mr-2 mb-1" name="name" type="text" value="{{ request('name') }}"
                       placeholder="{{ __('Search...') }}">

                <div class="mr-2 mb-1">
                    <select name="status" class="form-control form-control-sm">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option value="subscribed" {{ request('status') == 'subscribed' ? 'selected' : '' }}>{{ __('Subscribed') }}</option>
                        <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>{{ __('Unsubscribed') }}</option>
                        <option value="no_tags" {{ request('status') == 'no_tags' ? 'selected' : '' }}>{{ __('Not has tags') }}</option>
                        <option value="no_locations" {{ request('status') == 'no_locations' ? 'selected' : '' }}>{{ __('Not has locations') }}</option>
                    </select>
                </div>

                @if(count($tags))
                    <div class="mr-2 mb-1">
                        <select multiple class="selectpicker form-control form-control-sm" name="tags[]" data-width="auto" title="{{ __('Tags') }}">
                            @foreach($tags as $tagId => $tagName)
                                <option value="{{ $tagId }}" @if(in_array($tagId, request()->get('tags') ?? [])) selected @endif>{{ $tagName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(count($locations))
                    <div class="mr-2 mb-1">
                        <select multiple class="selectpicker form-control form-control-sm" name="locations[]" data-width="auto" title="{{ __('Locations') }}">
                            @foreach($locations as $locationId => $locationName)
                                <option value="{{ $locationId }}" @if(in_array($locationId, request()->get('locations') ?? [])) selected @endif>{{ $locationName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(count($skills))
                    <div class="mr-2 mb-1">
                        <select multiple class="selectpicker form-control form-control-sm" name="skills[]" data-width="auto" title="{{ __('Skills') }}">
                            @foreach($skills as $skillId => $skillName)
                                <option value="{{ $skillId }}" @if(in_array($skillId, request()->get('skills') ?? [])) selected @endif>{{ $skillName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(count($industries))
                    <div class="mr-2 mb-1">
                        <select multiple class="selectpicker form-control form-control-sm" name="industries[]" data-width="auto" title="{{ __('Industries') }}">
                            @foreach($industries as $industryId => $industryName)
                                <option value="{{ $industryId }}" @if(in_array($industryId, request()->get('industries') ?? [])) selected @endif>{{ $industryName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(count($levels))
                    <div class="mr-2 mb-1">
                        <select multiple class="selectpicker form-control form-control-sm" name="levels[]" data-width="auto" title="{{ __('Levels') }}">
                            @foreach($levels as $levelId => $levelName)
                                <option value="{{ $levelId }}" @if(in_array($levelId, request()->get('levels') ?? [])) selected @endif>{{ $levelName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="btn btn-light btn-md mb-1">{{ __('Search') }}</button>

                @if(request()->anyFilled(['name', 'status', 'tags', 'locations', 'skills', 'industries', 'levels']))
                    <a href="{{ route('sendportal.subscribers.index') }}" class="btn btn-md btn-light mb-1">{{ __('Clear') }}</a>
                @endif
            </form>
        @endslot

        @slot('right')
            <div class="d-flex align-items-center flex-wrap">
                {{-- Column Visibility Toggle --}}
                <div class="dropdown mr-2 mb-1">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="columnToggleBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-columns mr-1"></i> {{ __('Columns') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="columnToggleBtn" id="columnToggleMenu" style="min-width: 200px;">
                        <div class="font-weight-bold mb-2">{{ __('Show/Hide Columns') }}</div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-email" data-column="col-email" checked>
                            <label class="custom-control-label" for="col-email">{{ __('Email') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-name" data-column="col-name" checked>
                            <label class="custom-control-label" for="col-name">{{ __('Name') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-tags" data-column="col-tags" checked>
                            <label class="custom-control-label" for="col-tags">{{ __('Tags') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-locations" data-column="col-locations" checked>
                            <label class="custom-control-label" for="col-locations">{{ __('Locations') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-skills" data-column="col-skills">
                            <label class="custom-control-label" for="col-skills">{{ __('Skills') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-industries" data-column="col-industries">
                            <label class="custom-control-label" for="col-industries">{{ __('Industries') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-levels" data-column="col-levels">
                            <label class="custom-control-label" for="col-levels">{{ __('Levels') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-updated" data-column="col-updated" checked>
                            <label class="custom-control-label" for="col-updated">{{ __('Updated') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-status" data-column="col-status" checked>
                            <label class="custom-control-label" for="col-status">{{ __('Status') }}</label>
                        </div>
                    </div>
                </div>

                <div class="btn-group mr-2 mb-1">
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
                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportModal">
                            <i class="fa fa-download color-gray-400 mr-2"></i> {{ __('Export Subscribers') }}
                        </a>
                    </div>
                </div>
                <a class="btn btn-light btn-md mr-2 mb-1" href="{{ route('sendportal.tags.index') }}">
                    <i class="fa fa-tag color-gray-400 mr-1"></i> {{ __('Tags') }}
                </a>
                <a class="btn btn-light btn-md mr-2 mb-1" href="{{ route('sendportal.locations.index') }}">
                    <i class="fa fa-location-arrow color-gray-400 mr-1"></i> {{ __('Locations') }}
                </a>
                <a class="btn btn-primary btn-md btn-flat mb-1" href="{{ route('sendportal.subscribers.create') }}">
                    <i class="fa fa-plus mr-1"></i> {{ __('New Subscriber') }}
                </a>
            </div>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table table-responsive">
            <table class="table" id="subscribersTable">
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll" class="select-all-checkbox">
                    </th>
                    <th class="col-email">{{ __('Email') }}</th>
                    <th class="col-name">{{ __('Name') }}</th>
                    <th class="col-tags">{{ __('Tags') }}</th>
                    <th class="col-locations">{{ __('Locations') }}</th>
                    <th class="col-skills" style="display:none;">{{ __('Skills') }}</th>
                    <th class="col-industries" style="display:none;">{{ __('Industries') }}</th>
                    <th class="col-levels" style="display:none;">{{ __('Levels') }}</th>
                    <th class="col-updated">{{ __('Updated') }}</th>
                    <th class="col-status">{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subscribers as $subscriber)
                    <tr>
                        <td>
                            <input type="checkbox" class="subscriber-checkbox" value="{{ $subscriber->id }}">
                        </td>
                        <td class="col-email">
                            <a href="{{ route('sendportal.subscribers.show', $subscriber->id) }}">
                                {{ $subscriber->email }}
                            </a>
                        </td>
                        <td class="col-name">{{ $subscriber->full_name }}</td>
                        <td class="col-tags">
                            @forelse($subscriber->tags as $tag)
                                <span class="badge badge-light">{{ $tag->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td class="col-locations">
                            @forelse($subscriber->locations as $location)
                                <span class="badge badge-light">{{ $location->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td class="col-skills" style="display:none;">
                            @forelse($subscriber->skills as $skill)
                                <span class="badge badge-info">{{ $skill->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td class="col-industries" style="display:none;">
                            @forelse($subscriber->industries as $industry)
                                <span class="badge badge-warning">{{ $industry->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td class="col-levels" style="display:none;">
                            @forelse($subscriber->levels as $level)
                                <span class="badge badge-success">{{ $level->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td class="col-updated">
                            <span title="{{ $subscriber->updated_at }}">{{ $subscriber->updated_at->diffForHumans() }}</span>
                        </td>
                        <td class="col-status">
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

    {{-- Export Modal --}}
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">
                        <i class="fa fa-download mr-2"></i>{{ __('Export Subscribers') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="exportForm" action="{{ route('sendportal.subscribers.export') }}" method="GET">
                    <div class="modal-body">
                        <p class="text-muted mb-3">{{ __('Select the columns you want to include in the export file:') }}</p>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="exportSelectAll">
                                <i class="fa fa-check-square mr-1"></i> {{ __('Select All') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportDeselectAll">
                                <i class="fa fa-square mr-1"></i> {{ __('Deselect All') }}
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-id" name="columns[]" value="id" checked>
                                    <label class="custom-control-label" for="exp-id">{{ __('ID') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-email" name="columns[]" value="email" checked>
                                    <label class="custom-control-label" for="exp-email">{{ __('Email') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-first_name" name="columns[]" value="first_name" checked>
                                    <label class="custom-control-label" for="exp-first_name">{{ __('First Name') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-last_name" name="columns[]" value="last_name" checked>
                                    <label class="custom-control-label" for="exp-last_name">{{ __('Last Name') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-tags" name="columns[]" value="tags" checked>
                                    <label class="custom-control-label" for="exp-tags">{{ __('Tags') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-locations" name="columns[]" value="locations" checked>
                                    <label class="custom-control-label" for="exp-locations">{{ __('Locations') }}</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-skills" name="columns[]" value="skills">
                                    <label class="custom-control-label" for="exp-skills">{{ __('Skills') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-industries" name="columns[]" value="industries">
                                    <label class="custom-control-label" for="exp-industries">{{ __('Industries') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-levels" name="columns[]" value="levels">
                                    <label class="custom-control-label" for="exp-levels">{{ __('Levels') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-status" name="columns[]" value="status" checked>
                                    <label class="custom-control-label" for="exp-status">{{ __('Status') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-created_at" name="columns[]" value="created_at" checked>
                                    <label class="custom-control-label" for="exp-created_at">{{ __('Created At') }}</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input export-col" id="exp-updated_at" name="columns[]" value="updated_at">
                                    <label class="custom-control-label" for="exp-updated_at">{{ __('Updated At') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="exportSubmitBtn">
                            <i class="fa fa-download mr-1"></i> {{ __('Export CSV') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

        // Export modal logic
        document.getElementById('exportSelectAll').addEventListener('click', function() {
            document.querySelectorAll('.export-col').forEach(cb => cb.checked = true);
        });
        document.getElementById('exportDeselectAll').addEventListener('click', function() {
            document.querySelectorAll('.export-col').forEach(cb => cb.checked = false);
        });
        document.getElementById('exportSubmitBtn').addEventListener('click', function(e) {
            const checked = document.querySelectorAll('.export-col:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('{{ __("Please select at least one column to export.") }}');
            }
        });
    </script>

@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/css/bootstrap-select.min.css">
    <style>
        #columnToggleMenu {
            min-width: 220px;
        }
        #columnToggleMenu .custom-control-label {
            cursor: pointer;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========== Column Visibility Toggle ==========
        const STORAGE_KEY = 'subscriber_columns';
        let savedColumns = {};
        try {
            savedColumns = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
        } catch(e) { savedColumns = {}; }

        // Default visibility config
        const defaultVisible = {
            'col-email': true,
            'col-name': true,
            'col-tags': true,
            'col-locations': true,
            'col-skills': false,
            'col-industries': false,
            'col-levels': false,
            'col-updated': true,
            'col-status': true,
        };

        // Apply saved or default column visibility
        document.querySelectorAll('.col-toggle').forEach(function(checkbox) {
            const colName = checkbox.dataset.column;
            const isVisible = savedColumns.hasOwnProperty(colName) ? savedColumns[colName] : defaultVisible[colName];
            checkbox.checked = isVisible;
            toggleColumn(colName, isVisible);
        });

        // Handle toggle change
        document.querySelectorAll('.col-toggle').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const colName = this.dataset.column;
                const isVisible = this.checked;
                toggleColumn(colName, isVisible);
                // Save to localStorage
                savedColumns[colName] = isVisible;
                localStorage.setItem(STORAGE_KEY, JSON.stringify(savedColumns));
            });
        });

        function toggleColumn(colClass, show) {
            document.querySelectorAll('.' + colClass).forEach(function(el) {
                el.style.display = show ? '' : 'none';
            });
        }

        // Prevent dropdown from closing on checkbox click
        document.getElementById('columnToggleMenu').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // ========== Import Progress ==========
        if (document.querySelector('.import-processing')) {
            function checkProgress() {
                fetch('/api/v1/import-processing')
                    .then(response => response.json())
                    .then(data => {
                        updateProgressBar(data);
                        if (data.completed_chunks && data.total_chunks && 
                            data.completed_chunks >= data.total_chunks) {
                            showCompletedMessage();
                            setTimeout(() => { window.location.reload(); }, 2000);
                        } else {
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
                    statusText.textContent = `Đã xử lý ${data.completed_chunks}/${data.total_chunks} chunks (${progress}%)`;
                    if (data.estimated_time) {
                        timeRemaining.textContent = `Thời gian còn lại: ${formatTime(data.estimated_time)}`;
                    }
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
                alert.innerHTML = `<strong>Import hoàn tất!</strong><br>Trang sẽ tự động tải lại sau 2 giây...`;
            }

            function showErrorMessage(error) {
                const alert = document.querySelector('.import-processing');
                alert.classList.remove('alert-info');
                alert.classList.add('alert-danger');
                alert.innerHTML = `<strong>Có lỗi xảy ra!</strong><br>${error.message}`;
            }

            checkProgress();
        }

        // ========== Bulk Selection ==========
        const selectAllCheckbox = document.getElementById('selectAll');
        const subscriberCheckboxes = document.getElementsByClassName('subscriber-checkbox');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

        selectAllCheckbox.addEventListener('change', function() {
            Array.from(subscriberCheckboxes).forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });

        Array.from(subscriberCheckboxes).forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateDeleteButton();
                selectAllCheckbox.checked = Array.from(subscriberCheckboxes).every(cb => cb.checked);
            });
        });

        function updateDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
            deleteSelectedBtn.style.display = checkedBoxes.length > 0 ? 'inline-block' : 'none';
        }

        deleteSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
            const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

            if (selectedIds.length === 0) return;

            if (confirm('Bạn có chắc chắn muốn xóa ' + selectedIds.length + ' subscriber đã chọn?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("sendportal.subscribers.destroy-all") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

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
