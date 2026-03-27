<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sendportal\Base\Facades\Sendportal;

class SkillStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('sendportal_skills')
                    ->where('workspace_id', Sendportal::currentWorkspaceId()),
            ],
            'parent_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The skill name must be unique.'),
        ];
    }
}
