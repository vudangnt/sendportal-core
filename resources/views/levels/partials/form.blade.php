<div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label">{{ __('Name') }}</label>
    <div class="col-sm-10">
        <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $level->name ?? '') }}" required autofocus>
        @if($errors->has('name'))
            <span class="invalid-feedback">{{ $errors->first('name') }}</span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="parent_id" class="col-sm-2 col-form-label">{{ __('Parent') }}</label>
    <div class="col-sm-10">
        <select id="parent_id" name="parent_id" class="form-control">
            <option value="0">{{ __('None (Top Level)') }}</option>
            @foreach($parentLevels as $parentLevel)
                <option value="{{ $parentLevel->id }}" {{ (old('parent_id', $level->parent_id ?? 0)) == $parentLevel->id ? 'selected' : '' }}>
                    {{ $parentLevel->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
