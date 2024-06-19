@foreach($subtags as $childTag)
    <li>
        <div class="actions form-group" style="margin-left: auto;">
            <span>{{ $childTag['name'] }} ({{ $childTag['active_subscribers_count'] }} {{ __('subscribers') }} )</span>
            <a class="pl-3"
               href="{{ route('sendportal.tags.edit', $childTag['id']) }}"
            > <i class="fa fa-edit" aria-hidden="false"></i>
            </a>
            <form class="pl-3" action="{{ route('sendportal.tags.destroy', $childTag['id']) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class=" fa fa-trash" style="color: red; border:none "></button>
            </form>
        </div>
    </li>
@endforeach

<style>
    .form-group {
        margin: 0 auto 1rem auto;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
</style>
