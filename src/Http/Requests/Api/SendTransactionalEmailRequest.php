<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Sendportal\Base\Services\Transactional\AttachmentFetcher;

class SendTransactionalEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usingTemplate = filled($this->input('template_code'));

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

            'subject' => ($usingTemplate ? 'nullable' : 'required') . '|string|max:998',

            'content' => $usingTemplate ? 'nullable|array' : 'required|array',
            'content.type' => ($usingTemplate ? 'nullable' : 'required') . '|in:html,mime',
            'content.html' => 'nullable|string',
            'content.text' => 'nullable|string',
            'content.mime' => 'nullable|string',

            'template_code' => 'nullable|string|max:64|regex:/^[a-z0-9 _-]+$/',
            'variables'     => 'nullable|array',

            // Files are downloaded from these public URLs at request time and
            // attached to the email (see AttachmentFetcher for the size/SSRF rules).
            'attachments'              => 'nullable|array|max:' . AttachmentFetcher::MAX_COUNT,
            'attachments.*.url'        => 'required|url|max:2048',
            'attachments.*.filename'   => 'nullable|string|max:255',

            'tracking' => 'nullable|array',
            'tracking.open' => 'nullable|boolean',
            'tracking.click' => 'nullable|boolean',

            'metadata' => 'nullable|array',
        ];
    }
}
