<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendTransactionalEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => 'required|array',
            'from.email' => 'required|email',
            'from.name' => 'nullable|string|max:255',

            'to' => 'required|array|min:1',
            'to.*.email' => 'required|email',
            'to.*.name' => 'nullable|string|max:255',

            'cc' => 'nullable|array',
            'cc.*.email' => 'required|email',
            'cc.*.name' => 'nullable|string|max:255',

            'bcc' => 'nullable|array',
            'bcc.*.email' => 'required|email',
            'bcc.*.name' => 'nullable|string|max:255',

            'subject' => 'required|string|max:998',

            'content' => 'required|array',
            'content.type' => 'required|in:html,mime',
            'content.html' => 'required_if:content.type,html|string',
            'content.text' => 'nullable|string',
            'content.mime' => 'required_if:content.type,mime|string',

            'tracking' => 'nullable|array',
            'tracking.open' => 'nullable|boolean',
            'tracking.click' => 'nullable|boolean',

            'metadata' => 'nullable|array',
        ];
    }
}
