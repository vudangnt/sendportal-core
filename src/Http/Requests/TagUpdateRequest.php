<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Tags\Dimension;

class TagUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('sendportal_tags')
                    ->where('workspace_id', Sendportal::currentWorkspaceId())
                    ->ignore($this->tag),
            ],
            // dimension phải nằm trong danh mục, nếu không chuẩn hoá PHP và SQL lệch nhau
            // và rule âm thầm chọn sai người — xem đầu Task 8.
            'dimension' => ['nullable', 'string', Rule::in(Dimension::ALL)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The tag name must be unique.'),
        ];
    }
}
