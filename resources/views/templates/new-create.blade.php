@extends('sendportal::layouts.app')

@section('title', __('New Template'))

@section('heading')
    {{ __('Templates') }}
@stop

@section('content')
    <form class="" action="">
        <label for="email">Template Name:</label>
        <input id="id-field-name" class="form-control" name="name" type="text" value="{{ old('name', $template->name ?? '') }}">
        <iframe src="http://localhost:9000/" width="100%" height="700px" style="margin-top: 20px"></iframe>
        <br>
        <button id="btn-save-template" class="btn btn-primary btn-md" type="button">{{ __('Save Template') }}</button>
    </form>
@stop

@push('js')
    <script>
        var template = '';

        window.addEventListener('message', function(event) {
            //console.log(event.data); // Message received from child

            if (event.data) {
                template = event.data
            }
        });

        $("#btn-save-template").click(function() {
            var token = "{{ csrf_token() }}";

            let name = $('#id-field-name').val();

            if (name == '' || name == null) return toastr.error('Bạn chưa nhập tên')
            if (template == '' || template == null) return toastr.error('Nhấn Publish - để tạo template')

            $.ajax({
                url: "/templates",
                type: "POST",
                data: {
                    '_token': token,
                    'name' : name,
                    'content': template,
                },
                //dataType: 'json',
                success: function (response) {
                    window.location = "{{ route('sendportal.templates.index') }}";
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    return toastr.error(JSON.stringify(err.errors))
                }
            });
        });


    </script>
@endpush
