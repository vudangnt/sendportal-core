<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Sendportal\Base\Rules\CanAccessLocation;
use Sendportal\Base\Rules\CanAccessTag;

class SubscriberLocationDestroyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'locations' => ['array', 'required'],
            'locations.*' => ['integer', new CanAccessLocation($this->user())]
        ];
    }
}
