<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sendportal\Base\Facades\Sendportal;

class WorkspaceStorageUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'workspace_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('workspaces', 'name')
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:32'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'workspace_name.unique' => __('Workspace đã tồn tại.'),
            'email.unique' => __('Email đã tồn tại.'),
        ];
    }
}
