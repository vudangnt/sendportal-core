<?php

declare(strict_types=1);

namespace Sendportal\Base\Support;

/**
 * Skill names arrive as comma-separated strings, but a name may legitimately
 * contain a comma inside parentheses — "Tin học văn phòng (Word, Excel,
 * PowerPoint)". Splitting those blindly shredded one skill into three rows,
 * so these helpers both prevent new damage and describe how to repair the old.
 */
class SkillName
{
    /** Split a comma-separated list, ignoring commas nested in parentheses. */
    public static function splitList(string $raw): array
    {
        $names = [];
        $current = '';
        $depth = 0;

        foreach (preg_split('//u', $raw, -1, PREG_SPLIT_NO_EMPTY) as $char) {
            if ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                $depth = max(0, $depth - 1);
            } elseif ($char === ',' && $depth === 0) {
                $names[] = $current;
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $names[] = $current;

        return array_values(array_filter(array_map('trim', $names), fn ($name) => $name !== ''));
    }

    /** Repair a name left dangling by the old splitter; well-formed names pass through. */
    public static function canonical(string $name): string
    {
        $name = trim($name);
        $open = mb_substr_count($name, '(');
        $close = mb_substr_count($name, ')');

        if ($open > $close && ($cut = mb_strpos($name, '(')) !== false) {
            $repaired = trim(mb_substr($name, 0, $cut));
        } elseif ($close > $open) {
            $repaired = trim(str_replace(')', '', $name));
        } else {
            return $name;
        }

        return $repaired === '' ? $name : $repaired;
    }

    /** Whether a name survived the old splitter intact. */
    public static function isWellFormed(string $name): bool
    {
        return self::canonical($name) === trim($name);
    }

    /**
     * Key under which variants of one skill belong together. Symbols that carry
     * meaning survive as words, so C, C# and C++ stay three separate skills.
     */
    public static function groupKey(string $name): string
    {
        $key = mb_strtolower(self::canonical($name), 'UTF-8');
        $key = str_replace(['#', '+'], [' sharp ', ' plus '], $key);
        $key = trim((string) preg_replace('/[^\p{L}\p{N}]+/u', '_', $key), '_');

        return $key === '' ? 'x' . md5($name) : $key;
    }
}
