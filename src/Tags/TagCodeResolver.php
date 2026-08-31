<?php

declare(strict_types=1);

namespace Sendportal\Base\Tags;

use InvalidArgumentException;
use Sendportal\Base\Models\TagAlias;

/**
 * Tra mã tag cho một tên tự do: alias (do người vận hành khai) thắng normalizer.
 *
 * Trả NULL khi không chuẩn hoá được — KHÔNG tự tạo tag. Caller (ingest, backfill) gom
 * các chuỗi này lại để người duyệt, xem TagTaxonomyReport. Đây là chỗ chặn "tag rác".
 */
class TagCodeResolver
{
    /** @var string[] */
    private array $unresolved = [];

    public function resolve(int $workspaceId, string $dimension, string $rawName): ?string
    {
        $alias = TagAlias::where('workspace_id', $workspaceId)
            ->where('dimension', $dimension)
            ->where('alias', TagAlias::normaliseAlias($rawName))
            ->value('code');

        if ($alias) {
            return $alias;
        }

        try {
            return TagCode::make($dimension, $rawName)->code;
        } catch (InvalidArgumentException) {
            $this->unresolved[] = $rawName;

            return null;
        }
    }

    /** @return string[] các chuỗi không chuẩn hoá được trong vòng đời request/lệnh này */
    public function unresolved(): array
    {
        return array_values(array_unique($this->unresolved));
    }
}
