@extends('sendportal::layouts.app')

@section('title', __('Delete Campaign'))

@section('heading')
    @lang('Delete Campaign') - {{ $campaign->name }}
@endsection

@section('content')

    @component('sendportal::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.campaigns.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('Create Campaign') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header card-header-accent">
            <div class="card-header-inner">
                {{ __('Confirm Delete') }}
            </div>
        </div>
        <div class="card-body">
            <p>
                {!! __('Are you sure that you want to delete the <b>:name</b> campaign?', ['name' => $campaign->name]) !!}
            </p>
            <form action="{{ route('sendportal.campaigns.destroy', $campaign->id) }}" method="post" id="delete-campaign-form">
                @csrf
                @method('DELETE')
                <input type="hidden" name="delete_type" id="delete_type_hidden" value="hide">
                
                <div class="form-group">
                    <label>{{ __('Chọn phương thức xóa:') }}</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delete_type" id="delete_type_hide" value="hide" checked>
                        <label class="form-check-label" for="delete_type_hide">
                            <strong>{{ __('Ẩn campaign') }}</strong>
                            <small class="d-block text-muted">{{ __('Campaign sẽ bị ẩn khỏi danh sách nhưng vẫn có thể khôi phục sau này.') }}</small>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delete_type" id="delete_type_force" value="force" {{ !$campaign->draft ? 'disabled' : '' }}>
                        <label class="form-check-label" for="delete_type_force">
                            <strong>{{ __('Xóa vĩnh viễn') }}</strong>
                            <small class="d-block text-muted">
                                {{ $campaign->draft ? __('Xóa hoàn toàn campaign khỏi hệ thống. Không thể khôi phục.') : __('Chỉ có thể xóa vĩnh viễn campaign ở trạng thái draft.') }}
                            </small>
                        </label>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('sendportal.campaigns.index') }}" class="btn btn-md btn-light">{{ __('Hủy') }}</a>
                    <button type="submit" class="btn btn-md btn-danger" id="delete-submit-btn">{{ __('Xác nhận xóa') }}</button>
                </div>
            </form>
        </div>
        
        @push('js')
        <script>
            $(function() {
                var form = $('#delete-campaign-form');
                var submitBtn = $('#delete-submit-btn');
                var forceDeleteRadio = $('#delete_type_force');
                var hideRadio = $('#delete_type_hide');
                var hiddenInput = $('#delete_type_hidden');
                
                // Update hidden input when radio changes
                hideRadio.on('change', function() {
                    if ($(this).is(':checked')) {
                        hiddenInput.val('hide');
                        submitBtn.text('{{ __('Xác nhận xóa') }}');
                    }
                });
                
                forceDeleteRadio.on('change', function() {
                    if ($(this).is(':checked') && !$(this).prop('disabled')) {
                        hiddenInput.val('force');
                        submitBtn.removeClass('btn-danger').addClass('btn-danger');
                        submitBtn.text('{{ __('XÁC NHẬN XÓA VĨNH VIỄN') }}');
                    }
                });
                
                form.on('submit', function(e) {
                    var deleteType = $('input[name="delete_type"]:checked').val() || hiddenInput.val();
                    
                    // Ensure delete_type is set
                    if (!deleteType) {
                        deleteType = 'hide';
                    }
                    
                    // Update hidden input before submit
                    hiddenInput.val(deleteType);
                    
                    if (deleteType === 'force') {
                        if (!confirm('{{ __('Bạn có chắc chắn muốn XÓA VĨNH VIỄN campaign này? Hành động này không thể hoàn tác!') }}')) {
                            e.preventDefault();
                            return false;
                        }
                    } else {
                        if (!confirm('{{ __('Bạn có chắc chắn muốn ẩn campaign này?') }}')) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            });
        </script>
        @endpush
    </div>

@endsection
