<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sendportal\Base\Facades\Sendportal;

class LocationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('sendportal_locations', 'name')
                    ->where('workspace_id', Sendportal::currentWorkspaceId()),
            ],
            'type' => [
                'required',
                Rule::in(['city', 'country', 'state'])
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The location name must be unique.'),
            'type.required' => __('The type location must be required. (city, country, state)'),
        ];
    }
}
