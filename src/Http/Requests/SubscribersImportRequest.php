<?php

namespace Sendportal\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribersImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // size in kb
        $size = 1000 * 1024;

        return [
            'file' => 'required|file|max:' . $size . '|mimetypes:text/csv,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:sendportal_tags,id'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['exists:sendportal_locations,id'],
        ];
    }
}
