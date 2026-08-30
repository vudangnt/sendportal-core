<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

class TagAlias extends BaseModel
{
    protected $table = 'sendportal_tag_aliases';

    protected $fillable = ['workspace_id', 'dimension', 'alias', 'code'];

    public static function normaliseAlias(string $raw): string
    {
        return mb_strtolower(trim($raw));
    }
}
