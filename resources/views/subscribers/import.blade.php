@extends('sendportal::layouts.app')

@section('title', __('Import Subscribers'))

@section('heading')
    {{ __('Import Subscribers') }}
@stop

@section('content')

    @if (isset($errors) and count($errors->getBags()))
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->getBags() as $key => $bag)
                            @foreach($bag->all() as $error)
                                <li>{{ $key }} - {{ $error }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @component('sendportal::layouts.partials.card')
        @slot('cardHeader', __('Import via CSV, XSLX file'))

        @slot('cardBody')
            <p><b>{{ __('CSV, XSLX format') }}:</b> {{ __('Format your CSV, XSLX the same way as the example below (with the first title row). Use the ID or email columns if you want to update a Subscriber instead of creating it.') }}</p>

            <div class="table-responsive">
                <table class="table table-bordered table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('id') }}</th>
                            <th>{{ __('email') }}</th>
                            <th>{{ __('first_name') }}</th>
                            <th>{{ __('last_name') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>me@sendportal.io</td>
                            <td>Myself</td>
                            <td>Included</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form action="{{ route('sendportal.subscribers.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label for="file" class="col-sm-2 col-form-label">{{ __('File') }}</label>
                    <div class="col-sm-10">
                        <input type="file" name="file" class="form-control-file">
                        <small class="form-text text-muted">{{ __('Accepted formats: .csv, .xlsx') }}</small>
                    </div>
                </div>

                {{-- Tags --}}
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{ __('Tags') }}</label>
                    <div class="col-sm-10">
                        <div class="import-tag-selector" data-field="tags">
                            <div class="selected-tags mb-2" id="selected-tags"></div>
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm tag-search" placeholder="{{ __('Search tags...') }}" data-target="tags-dropdown">
                                <div class="tag-dropdown" id="tags-dropdown">
                                    @foreach($tags as $id => $name)
                                        <label class="tag-option">
                                            <input type="checkbox" name="tags[]" value="{{ $id }}">
                                            <span>{{ $name }}</span>
                                        </label>
                                    @endforeach
                                    @if(empty($tags) || count($tags) === 0)
                                        <div class="text-muted px-3 py-2">{{ __('No tags available') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Locations --}}
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{ __('Locations') }}</label>
                    <div class="col-sm-10">
                        <div class="import-tag-selector" data-field="locations">
                            <div class="selected-tags mb-2" id="selected-locations"></div>
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm tag-search" placeholder="{{ __('Search locations...') }}" data-target="locations-dropdown">
                                <div class="tag-dropdown" id="locations-dropdown">
                                    @foreach($locations as $id => $name)
                                        <label class="tag-option">
                                            <input type="checkbox" name="locations[]" value="{{ $id }}">
                                            <span>{{ $name }}</span>
                                        </label>
                                    @endforeach
                                    @if(empty($locations) || count($locations) === 0)
                                        <div class="text-muted px-3 py-2">{{ __('No locations available') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Skills --}}
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{ __('Skills') }}</label>
                    <div class="col-sm-10">
                        <div class="import-tag-selector" data-field="skills">
                            <div class="selected-tags mb-2" id="selected-skills"></div>
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm tag-search" placeholder="{{ __('Search skills...') }}" data-target="skills-dropdown">
                                <div class="tag-dropdown" id="skills-dropdown">
                                    @foreach($skills as $id => $name)
                                        <label class="tag-option">
                                            <input type="checkbox" name="skills[]" value="{{ $id }}">
                                            <span>{{ $name }}</span>
                                        </label>
                                    @endforeach
                                    @if(empty($skills) || count($skills) === 0)
                                        <div class="text-muted px-3 py-2">{{ __('No skills available') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Industries --}}
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{ __('Industries') }}</label>
                    <div class="col-sm-10">
                        <div class="import-tag-selector" data-field="industries">
                            <div class="selected-tags mb-2" id="selected-industries"></div>
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm tag-search" placeholder="{{ __('Search industries...') }}" data-target="industries-dropdown">
                                <div class="tag-dropdown" id="industries-dropdown">
                                    @foreach($industries as $id => $name)
                                        <label class="tag-option">
                                            <input type="checkbox" name="industries[]" value="{{ $id }}">
                                            <span>{{ $name }}</span>
                                        </label>
                                    @endforeach
                                    @if(empty($industries) || count($industries) === 0)
                                        <div class="text-muted px-3 py-2">{{ __('No industries available') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Levels --}}
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{ __('Levels') }}</label>
                    <div class="col-sm-10">
                        <div class="import-tag-selector" data-field="levels">
                            <div class="selected-tags mb-2" id="selected-levels"></div>
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm tag-search" placeholder="{{ __('Search levels...') }}" data-target="levels-dropdown">
                                <div class="tag-dropdown" id="levels-dropdown">
                                    @foreach($levels as $id => $name)
                                        <label class="tag-option">
                                            <input type="checkbox" name="levels[]" value="{{ $id }}">
                                            <span>{{ $name }}</span>
                                        </label>
                                    @endforeach
                                    @if(empty($levels) || count($levels) === 0)
                                        <div class="text-muted px-3 py-2">{{ __('No levels available') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                        <a href="{{ route('sendportal.subscribers.index') }}" class="btn btn-light ml-2">{{ __('Cancel') }}</a>
                    </div>
                </div>
            </form>
        @endSlot
    @endcomponent

@stop

@push('css')
<style>
    .import-tag-selector {
        position: relative;
    }
    .selected-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .selected-tag-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        background: #e9ecef;
        border-radius: 4px;
        font-size: 0.85rem;
        color: #495057;
    }
    .selected-tag-badge .remove-tag {
        margin-left: 5px;
        cursor: pointer;
        color: #dc3545;
        font-weight: bold;
        font-size: 14px;
        line-height: 1;
    }
    .selected-tag-badge .remove-tag:hover {
        color: #a71d2a;
    }
    .tag-search {
        border-radius: 4px;
    }
    .tag-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ced4da;
        border-top: 0;
        border-radius: 0 0 4px 4px;
        z-index: 1050;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .tag-dropdown.show {
        display: block;
    }
    .tag-option {
        display: flex;
        align-items: center;
        padding: 6px 12px;
        margin: 0;
        cursor: pointer;
        font-weight: normal;
        transition: background 0.15s;
    }
    .tag-option:hover {
        background: #f0f4ff;
    }
    .tag-option input[type="checkbox"] {
        margin-right: 8px;
    }
    .tag-option.hidden {
        display: none !important;
    }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    document.querySelectorAll('.tag-search').forEach(function(input) {
        const targetId = input.getAttribute('data-target');
        const dropdown = document.getElementById(targetId);

        input.addEventListener('focus', function() {
            // Close all other dropdowns
            document.querySelectorAll('.tag-dropdown.show').forEach(function(d) {
                if (d !== dropdown) d.classList.remove('show');
            });
            dropdown.classList.add('show');
        });

        input.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            dropdown.querySelectorAll('.tag-option').forEach(function(opt) {
                const text = opt.querySelector('span').textContent.toLowerCase();
                opt.classList.toggle('hidden', !text.includes(query));
            });
            dropdown.classList.add('show');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.import-tag-selector')) {
            document.querySelectorAll('.tag-dropdown.show').forEach(function(d) {
                d.classList.remove('show');
            });
        }
    });

    // Handle checkbox changes -> update badges
    document.querySelectorAll('.import-tag-selector').forEach(function(selector) {
        const field = selector.getAttribute('data-field');
        const badgeContainer = selector.querySelector('.selected-tags');

        selector.querySelectorAll('input[type="checkbox"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                updateBadges(selector, field, badgeContainer);
            });
        });

        function updateBadges(selector, field, container) {
            container.innerHTML = '';
            selector.querySelectorAll('input[type="checkbox"]:checked').forEach(function(cb) {
                const name = cb.closest('.tag-option').querySelector('span').textContent;
                const badge = document.createElement('span');
                badge.className = 'selected-tag-badge';
                badge.innerHTML = name + '<span class="remove-tag" data-value="' + cb.value + '">&times;</span>';
                container.appendChild(badge);
            });

            // Bind remove buttons
            container.querySelectorAll('.remove-tag').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const val = this.getAttribute('data-value');
                    const cb = selector.querySelector('input[type="checkbox"][value="' + val + '"]');
                    if (cb) {
                        cb.checked = false;
                        updateBadges(selector, field, container);
                    }
                });
            });
        }
    });
});
</script>
@endpush
