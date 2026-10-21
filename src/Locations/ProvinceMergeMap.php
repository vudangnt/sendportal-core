<?php

declare(strict_types=1);

namespace Sendportal\Base\Locations;

use InvalidArgumentException;

/**
 * Bản đồ ghép 63 -> 34 tỉnh/thành (NQ 202/2025/QH15) nạp từ JSON xuất từ RC
 * (DigiLib\Location\ProvinceMerge2025::all()). Chuẩn hoá tên PHẢI giữ y hệt nguồn đó:
 * bỏ dấu (kể cả đ), chữ thường, ký tự không phải chữ/số -> 1 khoảng trắng,
 * bỏ tiền tố tp / t p / thanh pho / tinh và hậu tố city / province / vietnam / viet nam / vn,
 * chỉ khớp NGUYÊN chuỗi.
 *
 * Lớp thuần, không truy cập DB.
 */
final class ProvinceMergeMap
{
    private const PREFIXES = ['thanh pho', 'tinh', 'tp', 't p'];
    private const SUFFIXES = ['city', 'province', 'viet nam', 'vietnam', 'vn'];

    private const ASCII_MAP = [
        'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
        'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        'đ' => 'd',
    ];

    /** @var array<string, array{code:string, name:string, old:array<int,string>, aliases:array<int,string>}> */
    private $provinces = [];

    /** @var array<string, string> khoá chuẩn hoá -> mã tỉnh mới */
    private $index = [];

    /**
     * @param array<int, array<string, mixed>> $entries
     */
    public function __construct(array $entries)
    {
        if ($entries === []) {
            throw new InvalidArgumentException('Province map is empty.');
        }

        foreach ($entries as $entry) {
            if (
                ! is_array($entry)
                || ! isset($entry['code'], $entry['name'])
                || ! isset($entry['old']) || ! is_array($entry['old']) || $entry['old'] === []
            ) {
                throw new InvalidArgumentException('Province map entry needs code, name and a non-empty old list.');
            }
            $code = (string) $entry['code'];
            $row = [
                'code' => $code,
                'name' => (string) $entry['name'],
                'old' => array_map('strval', array_values($entry['old'])),
                'aliases' => array_map('strval', array_values($entry['aliases'] ?? [])),
            ];
            $this->provinces[$code] = $row;

            $labels = array_merge([$row['name']], $row['old'], $row['aliases']);
            foreach ($labels as $label) {
                $key = self::normalize($label);
                if (! isset($this->index[$key])) {
                    $this->index[$key] = $code;
                }
            }
        }
    }

    public static function fromFile(string $path): self
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException("Map file not readable: {$path}");
        }
        $data = json_decode((string) file_get_contents($path), true);
        if (! is_array($data)) {
            throw new InvalidArgumentException("Map file is not valid JSON: {$path}");
        }

        return new self($data);
    }

    /** Tên tỉnh MỚI cho tên cũ / tên mới / bí danh khớp nguyên chuỗi; không khớp -> null. */
    public function resolve(?string $name): ?string
    {
        $province = $this->province($name);

        return $province === null ? null : $province['name'];
    }

    /**
     * @return array{code:string, name:string, old:array<int,string>, aliases:array<int,string>}|null
     */
    public function province(?string $name): ?array
    {
        if ($name === null) {
            return null;
        }
        $key = self::normalize($name);
        if ($key === '' || ! isset($this->index[$key])) {
            return null;
        }

        return $this->provinces[$this->index[$key]];
    }

    /** true khi nhận ra tỉnh nhưng tên khác tên mới và khác tên cũ của dòng được giữ. */
    public function isMergedAway(?string $name): bool
    {
        $province = $this->province($name);
        if ($province === null) {
            return false;
        }
        $key = self::normalize((string) $name);

        return $key !== self::normalize($province['name']) && $key !== self::normalize($province['old'][0]);
    }

    public static function normalize(string $s): string
    {
        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($s, \Normalizer::FORM_C);
            $s = $normalized ?: $s;
        }
        $s = strtr(mb_strtolower($s, 'UTF-8'), self::ASCII_MAP);
        $s = trim((string) preg_replace('/[^a-z0-9]+/', ' ', $s));
        if ($s === '') {
            return '';
        }
        do {
            $before = $s;
            foreach (self::PREFIXES as $p) {
                if (strncmp($s, $p . ' ', strlen($p) + 1) === 0) {
                    $s = substr($s, strlen($p) + 1);
                }
            }
            foreach (self::SUFFIXES as $p) {
                $tail = ' ' . $p;
                if (strlen($s) >= strlen($tail) && substr($s, -strlen($tail)) === $tail) {
                    $s = substr($s, 0, -strlen($tail));
                }
            }
        } while ($s !== $before);

        return $s;
    }
}
