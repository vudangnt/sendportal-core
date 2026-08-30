<?php

declare(strict_types=1);

namespace Sendportal\Base\Tags;

use InvalidArgumentException;

/**
 * Chuẩn hoá một tên tự do thành mã tag có prefix: "Hồ Chí Minh" + LOC → LOC_HO_CHI_MINH.
 *
 * Chuẩn hoá đặt ở SendPortal chứ không ở producer (xem spec QĐ-3a): ba repo RC/TH/NR cùng
 * đẩy tên tự do, để mỗi bên tự sinh mã thì bộ luật bị nhân bản và sẽ lệch nhau.
 *
 * Ném InvalidArgumentException khi không chuẩn hoá được — caller phải đưa vào hàng chờ duyệt,
 * TUYỆT ĐỐI không tự tạo tag đoán bừa (đúng thứ tài liệu muốn tránh: tag rác).
 */
final class TagCode
{
    public const MAX_LENGTH = 64;

    private function __construct(
        public readonly string $dimension,
        public readonly string $code,
    ) {
    }

    public static function make(string $dimension, string $rawName): self
    {
        if (! Dimension::isValid($dimension)) {
            throw new InvalidArgumentException("Dimension không hợp lệ: {$dimension}");
        }

        $slug = self::slug($rawName);

        if ($slug === '') {
            throw new InvalidArgumentException("Không chuẩn hoá được tên: \"{$rawName}\"");
        }

        $prefix = Dimension::prefix($dimension);

        // Idempotent: tên đã mang sẵn prefix thì không nhân đôi.
        if (str_starts_with($slug, $prefix)) {
            $code = $slug;
        } else {
            $code = $prefix . $slug;
        }

        return new self($dimension, substr($code, 0, self::MAX_LENGTH));
    }

    private static function slug(string $raw): string
    {
        // Ky hieu mang nghia phai song sot, khong thi C# va C++ cung ra mot ma.
        // Cung luat voi Sendportal\Base\Support\SkillName::groupKey().
        $s = self::removeDiacritics(trim($raw));
        $s = str_replace(['#', '+'], [' SHARP ', ' PLUS '], strtoupper($s));
        $s = trim((string) preg_replace('/[^A-Z0-9]+/', '_', $s), '_');

        if ($s !== '') {
            return $s;
        }

        // Ten khong thuoc bang Latin (vd 日本語) bi [^A-Z0-9] xoa sach. Van phai
        // addressable, nhung ma bam thi nguoi doc khong hieu -> Task 1.8 liet ke
        // ra de nguoi dat lai ten. Ten rong / toan dau cham cau thi tra '' cho
        // make() nem loi nhu cu.
        return preg_match('/[\p{L}\p{N}]/u', $raw) === 1
            ? 'X' . strtoupper(substr(md5($raw), 0, 10))
            : '';
    }

    private static function removeDiacritics(string $s): string
    {
        $map = [
            'a' => 'áàảãạăắằẳẵặâấầẩẫậ', 'e' => 'éèẻẽẹêếềểễệ', 'i' => 'íìỉĩị',
            'o' => 'óòỏõọôốồổỗộơớờởỡợ', 'u' => 'úùủũụưứừửữự', 'y' => 'ýỳỷỹỵ', 'd' => 'đ',
        ];

        foreach ($map as $plain => $accented) {
            $chars = preg_split('//u', $accented, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $s = str_replace($chars, $plain, $s);
            $s = str_replace(array_map('mb_strtoupper', $chars), strtoupper($plain), $s);
        }

        return $s;
    }
}
