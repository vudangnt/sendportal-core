<?php

declare(strict_types=1);

namespace Sendportal\Base\Tags;

/**
 * Danh mục dimension hợp lệ, theo tài liệu Lark CbyywcnNGiIMD8kUv7HlaElCgSf mục 3.
 * Chốt cứng ở đây để `UNIQUE(workspace_id, code)` không bị lách bởi prefix tự chế.
 */
final class Dimension
{
    /** @var string[] */
    public const ALL = [
        // chung
        'AUD', 'LOC', 'IND', 'LV', 'YOB', 'POS',
        // thùng chứa vận hành — KHÔNG có trong tài liệu Lark, thêm theo quyết định 29/08:
        // 27 gốc kiểu "[2026] Push job" là list marketer tạo ad-hoc, giữ lại thay vì bỏ.
        'LIST',
        // candidate
        'CAT', 'SKILL', 'LANG', 'APP',
        // employer
        'CITY', 'CTRY', 'CLI', 'SVC', 'CTYPE', 'CSIZE',
        // talent hunter
        'JOIN', 'REC_LV', 'REC_TYPE', 'REC_IND', 'REC_ST', 'YOE',
        // learner
        'GEN', 'CRS', 'ENR',
    ];

    public static function isValid(string $dimension): bool
    {
        return in_array($dimension, self::ALL, true);
    }

    public static function prefix(string $dimension): string
    {
        return $dimension . '_';
    }

    /** @return string[] */
    public static function audienceCodes(): array
    {
        return ['AUD_CANDIDATE', 'AUD_EMPLOYER', 'AUD_LEARNER', 'AUD_TALENTHUNTER', 'AUD_UNKNOWN'];
    }
}
