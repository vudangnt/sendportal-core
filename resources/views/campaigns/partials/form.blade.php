<x-sendportal.text-field name="name" :label="__('Campaign Name')" :value="$campaign->name ?? old('name')"/>
<x-sendportal.text-field name="subject" :label="__('Email Subject')" :value="$campaign->subject ?? old('subject')"/>
<x-sendportal.text-field name="from_name" :label="__('From Name')" :value="$campaign->from_name ?? old('from_name')"/>

@include('sendportal::campaigns.partials.template-picker')

<x-sendportal.select-field name="email_service_id" :label="__('Email Service')"
                           :options="$emailServices->pluck('formatted_name','id')"
                           :value="$campaign->email_service_id ?? old('email_service_id')"/>


<div class="form-group row form-group-from_email">
    <label for="id-field-from_email" class="control-label col-sm-3">{{ __('From Email') }}</label>
    <div class="col-sm-9">
        <div class="input-group">
            <input type="text" name="from_email_part" value="{{ $campaign->from_email ?? (old('from_email') ? explode('@', old('from_email'))[0] : '') }}"
                   id="id-field-from_email_part" class="form-control" placeholder="no-reply, info...">
            <div class="input-group-append">
                <span class="input-group-text" id="from-domain-addon" style="min-width: 160px;">@ {{ $campaign->from_domain ?? '' }}</span>
            </div>
        </div>
        <input type="hidden" name="from_email" id="full-from-email">
        @error('from_email')
            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
        @enderror
    </div>
</div>


<x-sendportal.checkbox-field name="is_open_tracking" :label="__('Track Opens')" value="1"
                             :checked="$campaign->is_open_tracking ?? true"/>
<x-sendportal.checkbox-field name="is_click_tracking" :label="__('Track Clicks')" value="1"
                             :checked="$campaign->is_click_tracking ?? true"/>

<x-sendportal.textarea-field name="content"
                             :label="__('Content')">{{ $campaign->content ?? old('content') }}</x-sendportal.textarea-field>

<div class="form-group row">
    <div class="offset-sm-3 col-sm-9">
        <a href="{{ route('sendportal.campaigns.index') }}" class="btn btn-light">
            <i class="fa fa-times mr-1"></i> {{ __('Cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-arrow-right mr-1"></i> {{ __('Save and continue') }}
        </button>
    </div>
</div>

@include('sendportal::layouts.partials.summernote')

@push('js')
    <script>

        $(document).ready(function () {
            $('form').on('submit', function (e) {
                // Get the values
                const emailPart = $('#id-field-from_email_part').val(); // The user-input email part
                const domainPart = $('#from-domain-addon').text().trim().replace('@', '').trim(); // The domain part

                // Combine into a full email address
                const fullEmail = emailPart + '@' + domainPart;

                // Set the combined email into the hidden input
                $('#full-from-email').val(fullEmail);

                // Optional: Log the full email for debugging
                console.log("Full Email:", fullEmail);
            });
        });



        const emailServiceData = @json($emailServices->mapWithKeys(function ($service) {
                return [$service->id => ['formatted_name' => $service->formatted_name, 'domain' => $service->domain]];
            }));

        $(function () {
            const smtp = {{
                $emailServices->filter(function ($service) {
                    return $service->type_id === \Sendportal\Base\Models\EmailServiceType::SMTP;
                })
                ->pluck('id')
            }};

            let service_id = $('select[name="email_service_id"]').val();
            console.log(service_id);
            const selectedDomain = emailServiceData[service_id];
            console.log(selectedDomain)
            if (selectedDomain) {
                const domain = selectedDomain.domain;
                console.log(domain);
                if (domain) {
                    $('#from-domain-addon').text('@ ' + domain);
                } else {
                    $('#from-domain-addon').text('@ ');
                }
            }

            toggleTracking(smtp.includes(parseInt(service_id, 10)));

            $('select[name="email_service_id"]').on('change', function () {
                let service_id = $(this).val();
                toggleTracking(smtp.includes(parseInt(this.value, 10)), service_id);
            });
        });

        function toggleTracking(disable, service_id) {

            let $open = $('input[name="is_open_tracking"]');
            let $click = $('input[name="is_click_tracking"]');

            if (disable) {
                $open.attr('disabled', 'disabled');
                $click.attr('disabled', 'disabled');
            } else {
                $open.removeAttr('disabled');
                $click.removeAttr('disabled');
            }
            console.log(service_id);

            const selectedDomain = emailServiceData[service_id];
            console.log(selectedDomain)
            if (selectedDomain) {
                const domain = selectedDomain.domain;
                console.log(domain);
                if (domain) {
                    $('#from-domain-addon').text('@ ' + domain);
                } else {
                    $('#from-domain-addon').text('@ ');
                }
            }
        }

    </script>
@endpush
