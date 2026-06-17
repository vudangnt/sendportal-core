<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Templates;

use Illuminate\Support\Facades\DB;

/**
 * Builds branding variables for transactional templates from the sending
 * workspace, merged with caller-provided overrides. Produces both scalar
 * variables and ready-made HTML composites used by the template shell:
 *   {{ brand_header_html }}  {{ brand_contact_html }}  {{ brand_social_html }}
 * plus scalars like {{ brand_name }}.
 *
 * Precedence: caller variables > workspace branding > empty.
 * Empty values degrade gracefully (no logo -> brand name text; no social -> hidden).
 */
class BrandingVariableProvider
{
    /** @return array<string, scalar|null> */
    public function forWorkspace(?int $workspaceId, array $callerVars = []): array
    {
        return $this->composeVariables($this->loadBrand($workspaceId), $callerVars);
    }

    /** @return array<string, string> */
    private function loadBrand(?int $workspaceId): array
    {
        $row = [];
        if ($workspaceId !== null) {
            $found = DB::table('workspaces')->where('id', $workspaceId)->first();
            $row = $found ? (array) $found : [];
        }

        return [
            'brand_name'          => !empty($row['company_name'])
                                        ? (string) $row['company_name'] : (string) ($row['name'] ?? ''),
            'logo_url'            => !empty($row['logo']) ? $this->asset((string) $row['logo']) : '',
            'brand_address'       => (string) ($row['brand_address'] ?? ''),
            'brand_support_email' => (string) ($row['brand_support_email'] ?? ''),
            'brand_hotline'       => (string) ($row['brand_hotline'] ?? ''),
            'brand_website'       => (string) ($row['brand_website_url'] ?? ''),
            'brand_linkedin'      => (string) ($row['brand_linkedin_url'] ?? ''),
            'brand_facebook'      => (string) ($row['brand_facebook_url'] ?? ''),
            'brand_zalo'          => (string) ($row['brand_zalo_url'] ?? ''),
            'brand_youtube'       => (string) ($row['brand_youtube_url'] ?? ''),
            'brand_tiktok'        => (string) ($row['brand_tiktok_url'] ?? ''),
            'brand_instagram'     => (string) ($row['brand_instagram_url'] ?? ''),
        ];
    }

    /**
     * Overlay caller overrides onto branding scalars, append composites,
     * and merge caller vars (candidate_name, job_title, company, ...).
     *
     * Composite HTML keys (brand_header_html, brand_contact_html,
     * brand_social_html) are always generated here and cannot be
     * overridden by unescaped caller-supplied values.
     *
     * @param array<string,string> $brand
     * @param array<string,scalar|null> $callerVars
     * @return array<string,scalar|null>
     */
    public function composeVariables(array $brand, array $callerVars = []): array
    {
        foreach ($this->brandKeys() as $k) {
            if (array_key_exists($k, $callerVars) && $callerVars[$k] !== null && $callerVars[$k] !== '') {
                $brand[$k] = (string) $callerVars[$k];
            }
        }

        $composites = [
            'brand_header_html'  => $this->headerHtml($brand),
            'brand_contact_html' => $this->contactHtml($brand),
            'brand_social_html'  => $this->socialHtml($brand),
        ];

        $callerSafe = array_diff_key($callerVars, array_flip([
            'brand_header_html', 'brand_contact_html', 'brand_social_html',
        ]));

        return array_merge($brand, $composites, $callerSafe);
    }

    public function headerHtml(array $b): string
    {
        $name = e((string) ($b['brand_name'] ?? ''));
        $logo = $this->safeUrl((string) ($b['logo_url'] ?? ''));
        if ($logo !== null) {
            return '<img src="' . e($logo) . '" alt="' . $name
                . '" style="max-height:42px;display:block;border:0;">';
        }
        return '<span style="font-size:21px;font-weight:800;color:#111827;letter-spacing:-.4px;">' . $name . '</span>';
    }

    public function contactHtml(array $b): string
    {
        $lines = [];
        if (!empty($b['brand_address'])) {
            $lines[] = e((string) $b['brand_address']);
        }
        $inline = [];
        if (!empty($b['brand_support_email']) && filter_var((string) $b['brand_support_email'], FILTER_VALIDATE_EMAIL)) {
            $email = e((string) $b['brand_support_email']);
            $inline[] = '<a href="mailto:' . $email . '" style="color:#8a90a2;text-decoration:none;">' . $email . '</a>';
        }
        if (!empty($b['brand_hotline'])) {
            $inline[] = e((string) $b['brand_hotline']);
        }
        if ($inline) {
            $lines[] = implode(' &middot; ', $inline);
        }
        if (!$lines) {
            return '';
        }
        return '<div style="font-size:12px;color:#8a90a2;line-height:1.7;margin-top:5px;">' . implode('<br>', $lines) . '</div>';
    }

    public function socialHtml(array $b): string
    {
        $items = [
            ['url' => $b['brand_website'] ?? '',   'label' => 'Web',  'title' => 'Website'],
            ['url' => $b['brand_linkedin'] ?? '',  'label' => 'in',   'title' => 'LinkedIn'],
            ['url' => $b['brand_facebook'] ?? '',  'label' => 'f',    'title' => 'Facebook'],
            ['url' => $b['brand_zalo'] ?? '',      'label' => 'Zalo', 'title' => 'Zalo'],
            ['url' => $b['brand_youtube'] ?? '',   'label' => 'YT',   'title' => 'YouTube'],
            ['url' => $b['brand_tiktok'] ?? '',    'label' => 'TT',   'title' => 'TikTok'],
            ['url' => $b['brand_instagram'] ?? '', 'label' => 'IG',   'title' => 'Instagram'],
        ];
        $cells = '';
        foreach ($items as $it) {
            $url = $this->safeUrl((string) ($it['url'] ?? ''));
            if ($url === null) {
                continue;
            }
            $cells .= '<a href="' . e($url) . '" target="_blank" rel="noopener" title="' . e($it['title']) . '" '
                . 'style="display:inline-block;width:32px;height:32px;line-height:32px;border-radius:50%;'
                . 'background:#eceff3;color:#5b6472;font-size:11px;font-weight:700;text-align:center;'
                . 'text-decoration:none;margin:0 3px;">' . $it['label'] . '</a>';
        }
        if ($cells === '') {
            return '';
        }
        return '<div style="margin-top:12px;">' . $cells . '</div>';
    }

    /** @return array<int,string> */
    private function brandKeys(): array
    {
        return [
            'brand_name', 'logo_url', 'brand_address', 'brand_support_email', 'brand_hotline',
            'brand_website', 'brand_linkedin', 'brand_facebook', 'brand_zalo',
            'brand_youtube', 'brand_tiktok', 'brand_instagram',
        ];
    }

    private function asset(string $path): string
    {
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        return rtrim((string) config('app.url'), '/') . '/' . ltrim($path, '/');
    }

    private function safeUrl(string $url): ?string
    {
        if ($url === '') {
            return null;
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }
}
