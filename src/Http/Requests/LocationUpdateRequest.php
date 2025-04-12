<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sendportal\Base\Facades\Sendportal;

class LocationUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('sendportal_locations')
                    ->where('workspace_id', Sendportal::currentWorkspaceId())
                    ->ignore($this->location),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The tag name must be unique.'),
        ];
    }
}
