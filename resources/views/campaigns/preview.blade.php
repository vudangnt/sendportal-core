@extends('sendportal::layouts.app')

@section('title', __('Confirm Campaign'))

@section('heading')
    {{ __('Preview Campaign') }}: {{ $campaign->name }}
@stop

@section('content')

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header card-header-accent">
                    <div class="card-header-inner">
                        {{ __('Content') }}
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal">
                        <div class="row">
                            <label class="col-sm-2 col-form-label">{{ __('From') }}:</label>
                            <div class="col-sm-10">
                                <b>
                                    <span class="form-control-plaintext">{{ $campaign->from_name . ' <' . $campaign->from_email . '>' }}</span>
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">{{ __('Subject') }}:</label>
                            <div class="col-sm-10">
                                <b>
                                    <span class="form-control-plaintext">{{ $campaign->subject }}</span>
                                </b>
                            </div>
                        </div>
                        <div style="border: 1px solid #ddd; height: 600px">
                            <iframe id="js-template-iframe" srcdoc="{{ $campaign->merged_content }}"
                                    class="embed-responsive-item" frameborder="0"
                                    style="height: 100%; width: 100%"></iframe>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">

            <form action="{{ route('sendportal.campaigns.test', $campaign->id) }}" method="POST">
                @csrf
                <div class="card mb-4">
                    <div class="card-header">
                        {{ __('Test Email') }}
                    </div>
                    <div class="card-body">
                        <div class="pb-2"><b>{{ __('RECIPIENT') }}</b></div>
                        <div class="form-group row form-group-schedule">
                            <div class="col-sm-12">
                                <input name="recipient_email" id="test-email-recipient" type="email"
                                       class="form-control" placeholder="{{ __('Recipient email address') }}">
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-sm btn-secondary">{{ __('Send Test Email') }}</button>
                        </div>
                    </div>
                </div>
            </form>

            <form action="{{ route('sendportal.campaigns.send', $campaign->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card mb-4">
                    <div class="card-header">
                        {{ __('Sending options') }}
                    </div>
                    <div class="card-body">

                        {{-- RECIPIENTS (Tags) --}}
                        <div class="pb-2"><b>{{ __('RECIPIENTS') }}</b></div>
                        <div class="form-group row form-group-recipients">
                            <div class="col-sm-12">
                                <select id="id-field-recipients" class="form-control" name="recipients">
                                    <option value="send_to_all" {{ (old('recipients') ? old('recipients') == 'send_to_all' : $campaign->send_to_all) ? 'selected' : '' }}>
                                        {{ __('All subscribers') }} ({{ $subscriberCount }})
                                    </option>
                                    <option value="send_to_tags" {{ (old('recipients') ? old('recipients') == 'send_to_tags' : !$campaign->send_to_all) ? 'selected' : '' }}>
                                        {{ __('Select Tags') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="tags-container tag-section {{ (old('recipients') ? old('recipients') == 'send_to_tags' : !$campaign->send_to_all) ? '' : 'hide' }}">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <input type="text" class="form-control form-control-sm tag-search" data-target="tags-list" placeholder="{{ __('Search tags...') }}" style="width: 60%;">
                                <div>
                                    <a href="#" class="btn btn-xs btn-outline-primary select-all-btn" data-target="tags-list">{{ __('All') }}</a>
                                    <a href="#" class="btn btn-xs btn-outline-secondary deselect-all-btn" data-target="tags-list">{{ __('None') }}</a>
                                </div>
                            </div>
                            <div class="tags-list tag-list-scrollable" style="max-height: 250px; overflow-y: auto;">
                                @forelse($tags as $tag)
                                    @if ($tag['parent_id'] == 0)
                                        <div class="checkbox tag-item tag_{{ $tag['id'] }}">
                                            <input class="parent-checkbox" name="tags[]" type="checkbox" value="{{ $tag['id'] }}" id="tag-{{ $tag['id'] }}">
                                            <label for="tag-{{ $tag['id'] }}">
                                                <span class="parent-tag" data-parent="{{ $tag['id'] }}">
                                                    {{ $tag['name'] }}
                                                    <span class="badge badge-secondary badge-pill">{{ $tag['active_subscribers_count'] }}</span>
                                                </span>
                                            </label>
                                            @if ($tag['child_count'] > 0)
                                                @foreach($tag['child'] as $tagChild)
                                                    <div class="checkbox pl-3 child_of_{{ $tag['id'] }} tag-item" style="display: none;">
                                                        <input class="child-checkbox" name="tags[]" type="checkbox" value="{{ $tagChild['id'] }}" id="tag-{{ $tagChild['id'] }}">
                                                        <label for="tag-{{ $tagChild['id'] }}">
                                                            {{ $tagChild['name'] }}
                                                            <span class="badge badge-secondary badge-pill">{{ $tagChild['active_subscribers_count'] }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                @empty
                                    <div class="text-muted">{{ __('There are no tags to select') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- LOCATIONS --}}
                        <div class="pb-2"><b>{{ __('RECIPIENTS LOCATION') }}</b></div>
                        <div class="form-group row form-group-recipients">
                            <div class="col-sm-12">
                                <select id="id-field-recipients_location" class="form-control" name="recipients_location">
                                    <option value="send_to_all_locations" {{ (old('recipients') ? old('recipients') == 'send_to_all' : $campaign->send_to_all) ? 'selected' : '' }}>
                                        {{ __('All locations') }}
                                    </option>
                                    <option value="send_to_locations" {{ (old('recipients') ? old('recipients') == 'send_to_locations' : !$campaign->send_to_all) ? 'selected' : '' }}>
                                        {{ __('Select Locations') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="locations-container tag-section {{ (old('recipients') ? old('recipients') == 'send_to_locations' : !$campaign->send_to_all) ? '' : 'hide' }}">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <input type="text" class="form-control form-control-sm tag-search" data-target="locations-list" placeholder="{{ __('Search locations...') }}" style="width: 60%;">
                                <div>
                                    <a href="#" class="btn btn-xs btn-outline-primary select-all-btn" data-target="locations-list">{{ __('All') }}</a>
                                    <a href="#" class="btn btn-xs btn-outline-secondary deselect-all-btn" data-target="locations-list">{{ __('None') }}</a>
                                </div>
                            </div>
                            <div class="locations-list tag-list-scrollable" style="max-height: 250px; overflow-y: auto;">
                                @forelse($locations as $location)
                                    @if ($location['parent_id'] == 0)
                                        <div class="checkbox tag-item tag_{{ $location['id'] }}">
                                            <input id="location-{{ $location['id'] }}" class="parent-checkbox" name="locations[]" type="checkbox" value="{{ $location['id'] }}">
                                            <label for="location-{{ $location['id'] }}">
                                                <span class="parent-tag" data-parent="{{ $location['id'] }}">
                                                    {{ $location['name'] }}
                                                    <span class="badge badge-secondary badge-pill">{{ $location['active_subscribers_count'] }}</span>
                                                </span>
                                            </label>
                                            @if ($location['child_count'] > 0)
                                                @foreach($location['child'] as $locationChild)
                                                    <div class="checkbox pl-3 child_of_{{ $location['id'] }} tag-item" style="display: none;">
                                                        <input class="child-checkbox" name="locations[]" type="checkbox" value="{{ $locationChild['id'] }}" id="location-{{ $locationChild['id'] }}">
                                                        <label for="location-{{ $locationChild['id'] }}">
                                                            {{ $locationChild['name'] }}
                                                            <span class="badge badge-secondary badge-pill">{{ $locationChild['active_subscribers_count'] }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                @empty
                                    <div class="text-muted">{{ __('There are no locations to select') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- SKILLS --}}
                        <div class="pb-2"><b>{{ __('SKILLS') }}</b></div>
                        <div class="form-group row form-group-recipients">
                            <div class="col-sm-12">
                                <select id="id-field-recipients_skills" class="form-control" name="recipients_skills">
                                    <option value="send_to_all_skills">{{ __('All skills') }}</option>
                                    <option value="send_to_skills">{{ __('Select Skills') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="skills-container tag-section hide">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <input type="text" class="form-control form-control-sm tag-search" data-target="skills-list" placeholder="{{ __('Search skills...') }}" style="width: 60%;">
                                <div>
                                    <a href="#" class="btn btn-xs btn-outline-primary select-all-btn" data-target="skills-list">{{ __('All') }}</a>
                                    <a href="#" class="btn btn-xs btn-outline-secondary deselect-all-btn" data-target="skills-list">{{ __('None') }}</a>
                                </div>
                            </div>
                            <div class="skills-list tag-list-scrollable" style="max-height: 200px; overflow-y: auto;">
                                @forelse($skills as $skill)
                                    <div class="checkbox tag-item">
                                        <input id="skill-{{ $skill['id'] }}" name="skills[]" type="checkbox" value="{{ $skill['id'] }}">
                                        <label for="skill-{{ $skill['id'] }}">
                                            {{ $skill['name'] }}
                                            <span class="badge badge-info badge-pill">{{ $skill['active_subscribers_count'] ?? 0 }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-muted">{{ __('There are no skills to select') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- INDUSTRIES --}}
                        <div class="pb-2"><b>{{ __('INDUSTRIES') }}</b></div>
                        <div class="form-group row form-group-recipients">
                            <div class="col-sm-12">
                                <select id="id-field-recipients_industries" class="form-control" name="recipients_industries">
                                    <option value="send_to_all_industries">{{ __('All industries') }}</option>
                                    <option value="send_to_industries">{{ __('Select Industries') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="industries-container tag-section hide">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <input type="text" class="form-control form-control-sm tag-search" data-target="industries-list" placeholder="{{ __('Search industries...') }}" style="width: 60%;">
                                <div>
                                    <a href="#" class="btn btn-xs btn-outline-primary select-all-btn" data-target="industries-list">{{ __('All') }}</a>
                                    <a href="#" class="btn btn-xs btn-outline-secondary deselect-all-btn" data-target="industries-list">{{ __('None') }}</a>
                                </div>
                            </div>
                            <div class="industries-list tag-list-scrollable" style="max-height: 200px; overflow-y: auto;">
                                @forelse($industries as $industry)
                                    <div class="checkbox tag-item">
                                        <input id="industry-{{ $industry['id'] }}" name="industries[]" type="checkbox" value="{{ $industry['id'] }}">
                                        <label for="industry-{{ $industry['id'] }}">
                                            {{ $industry['name'] }}
                                            <span class="badge badge-warning badge-pill">{{ $industry['active_subscribers_count'] ?? 0 }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-muted">{{ __('There are no industries to select') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- LEVELS --}}
                        <div class="pb-2"><b>{{ __('LEVELS') }}</b></div>
                        <div class="form-group row form-group-recipients">
                            <div class="col-sm-12">
                                <select id="id-field-recipients_levels" class="form-control" name="recipients_levels">
                                    <option value="send_to_all_levels">{{ __('All levels') }}</option>
                                    <option value="send_to_levels">{{ __('Select Levels') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="levels-container tag-section hide">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <input type="text" class="form-control form-control-sm tag-search" data-target="levels-list" placeholder="{{ __('Search levels...') }}" style="width: 60%;">
                                <div>
                                    <a href="#" class="btn btn-xs btn-outline-primary select-all-btn" data-target="levels-list">{{ __('All') }}</a>
                                    <a href="#" class="btn btn-xs btn-outline-secondary deselect-all-btn" data-target="levels-list">{{ __('None') }}</a>
                                </div>
                            </div>
                            <div class="levels-list tag-list-scrollable" style="max-height: 200px; overflow-y: auto;">
                                @forelse($levels as $level)
                                    <div class="checkbox tag-item">
                                        <input id="level-{{ $level['id'] }}" name="levels[]" type="checkbox" value="{{ $level['id'] }}">
                                        <label for="level-{{ $level['id'] }}">
                                            {{ $level['name'] }}
                                            <span class="badge badge-success badge-pill">{{ $level['active_subscribers_count'] ?? 0 }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-muted">{{ __('There are no levels to select') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- SCHEDULE --}}
                        <div class="pb-2"><b>{{ __('SCHEDULE') }}</b></div>
                        <div class="form-group row form-group-schedule">
                            <div class="col-sm-12">
                                <select id="id-field-schedule" class="form-control" name="schedule">
                                    <option value="now" {{ old('schedule') === 'now' || is_null($campaign->scheduled_at) ? 'selected' : '' }}>
                                        {{ __('Dispatch now') }}
                                    </option>
                                    <option value="scheduled" {{ old('schedule') === 'now' || $campaign->scheduled_at ? 'selected' : '' }}>
                                        {{ __('Dispatch at a specific time') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <input id="input-field-scheduled_at" class="form-control hide mb-3" name="scheduled_at"
                               type="text" value="{{ $campaign->scheduled_at ?: now() }}">

                        <div class="pb-2"><b>{{ __('SENDING BEHAVIOUR') }}</b></div>
                        <div class="form-group row form-group-schedule">
                            <div class="col-sm-12">
                                <select id="id-field-behaviour" class="form-control" name="behaviour">
                                    <option value="auto">{{ __('Send automatically') }}</option>
                                    <option value="draft">{{ __('Queue draft') }}</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div>
                    <a href="{{ route('sendportal.campaigns.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Send campaign') }}</button>
                </div>

            </form>

        </div>
    </div>

@stop

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .tag-section { margin-bottom: 5px; }
        .tag-list-scrollable {
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 8px;
            background: #fafafa;
        }
        .tag-list-scrollable .checkbox { margin-bottom: 2px; }
        .tag-list-scrollable label { font-weight: normal; cursor: pointer; margin-bottom: 0; }
        .tag-list-scrollable .badge-pill { font-size: 0.7rem; vertical-align: middle; }
        .tag-search { border-radius: 3px; }
        .parent-tag { cursor: pointer; font-weight: 600; }
        .select-all-btn, .deselect-all-btn { font-size: 0.7rem; padding: 2px 6px; }
        .tag-item.search-hidden { display: none !important; }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(function() {
            // ========== Toggle containers ==========
            $('#id-field-recipients').change(function () {
                $('.tags-container').toggleClass('hide', this.value === 'send_to_all');
            });
            $('#id-field-recipients_location').change(function () {
                $('.locations-container').toggleClass('hide', this.value === 'send_to_all_locations');
            });
            $('#id-field-recipients_skills').change(function () {
                $('.skills-container').toggleClass('hide', this.value === 'send_to_all_skills');
            });
            $('#id-field-recipients_industries').change(function () {
                $('.industries-container').toggleClass('hide', this.value === 'send_to_all_industries');
            });
            $('#id-field-recipients_levels').change(function () {
                $('.levels-container').toggleClass('hide', this.value === 'send_to_all_levels');
            });

            // ========== Schedule ==========
            var element = $('#input-field-scheduled_at');
            $('#id-field-schedule').change(function () {
                element.toggleClass('hide', this.value === 'now');
            });
            $('#input-field-scheduled_at').flatpickr({
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i",
            });

            // ========== Parent-Child tag toggle & check ==========
            $('.parent-tag').click(function() {
                var parent_id = $(this).data('parent');
                $('.child_of_' + parent_id).toggle();
            });
            $('.parent-checkbox').change(function() {
                var parent_id = $(this).val();
                if ($(this).is(':checked')) {
                    $('.child_of_' + parent_id + ' .child-checkbox').prop('checked', true);
                } else {
                    $('.child_of_' + parent_id + ' .child-checkbox').prop('checked', false);
                }
            });

            // ========== Search within tag lists ==========
            $('.tag-search').on('input', function() {
                var searchText = $(this).val().toLowerCase().trim();
                var targetClass = $(this).data('target');
                var $list = $('.' + targetClass);
                
                $list.find('.tag-item').each(function() {
                    var itemText = $(this).text().toLowerCase();
                    if (searchText === '' || itemText.indexOf(searchText) !== -1) {
                        $(this).removeClass('search-hidden');
                        if ($(this).find('.parent-tag').length && searchText !== '') {
                            $(this).find('[class*="child_of_"]').removeClass('search-hidden').show();
                        }
                    } else {
                        $(this).addClass('search-hidden');
                    }
                });
            });

            // ========== Select All / Deselect All ==========
            $('.select-all-btn').click(function(e) {
                e.preventDefault();
                var targetClass = $(this).data('target');
                $('.' + targetClass).find('input[type="checkbox"]:visible').not('.search-hidden input').prop('checked', true);
            });
            $('.deselect-all-btn').click(function(e) {
                e.preventDefault();
                var targetClass = $(this).data('target');
                $('.' + targetClass).find('input[type="checkbox"]').prop('checked', false);
            });
        });
    </script>
@endpush
