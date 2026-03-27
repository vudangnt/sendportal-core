<div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label">{{ __('Name') }}</label>
    <div class="col-sm-10">
        <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $skill->name ?? '') }}" required autofocus>
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
            @foreach($parentSkills as $parentSkill)
                <option value="{{ $parentSkill->id }}" {{ (old('parent_id', $skill->parent_id ?? 0)) == $parentSkill->id ? 'selected' : '' }}>
                    {{ $parentSkill->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
