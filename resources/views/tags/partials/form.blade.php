{{--<x-sendportal.text-field name="name" :label="__('Tag Name')" :value="$tag->name ?? null" />--}}

@csrf
<label for="name">Name:</label>
@if(isset($tag))
    <input type="text" id="name" name="name" value="{{$tag?->name??""}}">
@else
<input type="text" id="name" name="name" value="">
@endif
<label for="parent_id">Parent:</label>
<select id="parent_id" name="parent_id">
    <option value="0">None</option>
    @if(isset($tag))
        @foreach($parentTags as $parentTag)
            <option value="{{ $parentTag->id }}" {{ $tag->parent_id == $parentTag->id ? 'selected' : '' }}>
                {{ $parentTag->name }}
            </option>
        @endforeach
    @else
        @foreach($parentTags as $parentTag)
            <option value="{{ $parentTag->id }}">
                {{ $parentTag->name }}
            </option>
        @endforeach    @endif


</select>
