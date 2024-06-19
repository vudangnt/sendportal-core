<form action="{{ route('sendportal.locations.destroy', $location['id']) }}" method="POST">
    @csrf
    @method('DELETE')
    <a href="{{ route('sendportal.locations.edit', $location['id']) }}"
       class="btn btn-sm btn-light">{{ __('Edit') }}</a>
    <button type="submit" class="btn btn-sm btn-light">{{ __('Delete') }}</button>
</form>
