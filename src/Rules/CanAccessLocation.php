<?php

declare(strict_types=1);

namespace Sendportal\Base\Rules;

use Illuminate\Contracts\Validation\Rule;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Models\Location;

class CanAccessLocation implements Rule
{
    public function passes($attribute, $value): bool
    {
        $tag = Location::find($value);

        if (!$tag) {
            return false;
        }

        return $tag->workspace_id == Sendportal::currentWorkspaceId();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Location ID :input does not exist.';
    }
}
