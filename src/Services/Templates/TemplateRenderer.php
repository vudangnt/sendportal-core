<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Templates;

use Sendportal\Base\Models\Template;

class TemplateRenderer
{
    private const VAR_REGEX = '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/';

    /**
     * Render the template's subject and content with the given variables.
     *
     * @param array<string, scalar|null> $variables
     * @return array{subject: string, content: string}
     */
    public function render(Template $template, array $variables = []): array
    {
        return [
            'subject' => $this->substitute((string) ($template->subject ?? ''), $variables),
            'content' => $this->substitute((string) ($template->content ?? ''), $variables),
        ];
    }

    /**
     * Variable names referenced by the template (deduped, in first-seen order).
     *
     * @return array<int, string>
     */
    public function detectVariables(Template $template): array
    {
        $source = (string) ($template->subject ?? '') . "\n" . (string) ($template->content ?? '');
        preg_match_all(self::VAR_REGEX, $source, $matches);
        return array_values(array_unique($matches[1] ?? []));
    }

    private function substitute(string $haystack, array $vars): string
    {
        return preg_replace_callback(self::VAR_REGEX, static function ($m) use ($vars) {
            return array_key_exists($m[1], $vars) && $vars[$m[1]] !== null
                ? (string) $vars[$m[1]]
                : '';
        }, $haystack);
    }
}
