<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Templates;

/**
 * Builds a NATIVE multi-block Unlayer design (schemaVersion 16) for a structured
 * transactional template definition (see {@see TransactionalTemplateSeedData}).
 *
 * Unlike {@see UnlayerDesignBuilder} — which wraps a whole email in ONE opaque
 * "html" block (good only for arbitrary hand-written HTML) — this builder lays
 * the email out as discrete, editable Unlayer blocks:
 *
 *   Row 1  header  — html block: {{ brand_header_html }} (logo / brand name)
 *   Row 2  title   — text block: status badge + <h1> title          (EDITABLE)
 *   Row 3  body    — text block: copy paragraphs (+ button block)    (EDITABLE)
 *   Row 4  footer  — html block: {{ brand_name }} / contact / social
 *
 * The branding composites stay inside `html` blocks because they are server-side
 * HTML fragments injected at render time; keeping them as html blocks means an
 * edit → Save round-trip in the editor preserves the placeholders. The title,
 * body copy and CTA become first-class blocks that can be edited visually.
 */
class TransactionalTemplateDesignBuilder
{
    /** Tint (badge background) for each accent color. */
    private static function tint(string $accent): string
    {
        return [
            '#2563eb' => '#eaf1ff',
            '#16a34a' => '#e9f7ef',
            '#4f46e5' => '#eef0fd',
            '#7c3aed' => '#f3edfd',
            '#0d9488' => '#e6f6f4',
            '#475569' => '#eef1f5',
        ][$accent] ?? '#eef1f5';
    }

    /**
     * Build the Unlayer design array for a structured template definition.
     *
     * @param array{name:string,color:string,title:string,body:array<int,string>,button:?array{text:string,href:string}} $t
     */
    public static function design(array $t): array
    {
        $accent = $t['color'];
        $tint   = self::tint($accent);
        $hasButton = !empty($t['button']);

        $htmlCounter = 0;
        $textCounter = 0;
        $buttonCounter = 0;

        // ── Row 1: header (brand logo / name) ───────────────────────
        $headerHtml =
            '<div style="padding:24px 36px 16px;border-bottom:1px solid #eef0f4;'
            . 'border-top:4px solid ' . $accent . ';font-family:Arial,sans-serif;">'
            . '{{ brand_header_html }}</div>';
        $rowHeader = self::row('row_header', '#ffffff', [
            self::htmlBlock(++$htmlCounter, $headerHtml),
        ]);

        // ── Row 2: badge + title ────────────────────────────────────
        $titleText =
            '<p style="margin:0 0 12px;line-height:1;">'
            . '<span style="display:inline-block;background:' . $tint . ';color:' . $accent . ';'
            . 'font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;'
            . 'padding:5px 11px;border-radius:20px;">' . $t['name'] . '</span></p>'
            . '<h1 style="margin:0;font-size:23px;line-height:1.3;color:#111827;font-weight:700;">'
            . $t['title'] . '</h1>';
        $rowTitle = self::row('row_title', '#ffffff', [
            self::textBlock(++$textCounter, $titleText, '26px 36px 6px'),
        ]);

        // ── Row 3: body copy (+ optional CTA) ───────────────────────
        $bodyHtml = '';
        foreach ($t['body'] as $p) {
            $bodyHtml .= '<p style="margin:0 0 14px;">' . $p . '</p>';
        }
        $bodyContents = [
            self::textBlock(++$textCounter, $bodyHtml, '14px 36px 6px', '15px', '170%', '#3d4452'),
        ];
        if ($hasButton) {
            $bodyContents[] = self::buttonBlock(
                ++$buttonCounter,
                $t['button']['text'],
                $t['button']['href'],
                $accent
            );
        }
        $rowBody = self::row('row_body', '#ffffff', $bodyContents);

        // ── Row 4: footer (branding) ────────────────────────────────
        $footerHtml =
            '<div style="padding:24px 36px 26px;background:#f7f8fa;border-top:1px solid #eef0f4;'
            . 'font-family:Arial,sans-serif;">'
            . '<div style="font-size:14px;font-weight:700;color:#1f2430;">{{ brand_name }}</div>'
            . '{{ brand_contact_html }}'
            . '{{ brand_social_html }}'
            . '<div style="font-size:11px;color:#aab0bc;margin-top:14px;line-height:1.6;">'
            . '&copy; {{ brand_name }} &middot; You are receiving this email because you applied for a position.</div>'
            . '</div>';
        $rowFooter = self::row('row_footer', '#f7f8fa', [
            self::htmlBlock(++$htmlCounter, $footerHtml),
        ]);

        return [
            'counters' => [
                'u_row'             => 4,
                'u_column'          => 4,
                'u_content_html'    => $htmlCounter,
                'u_content_text'    => $textCounter,
                'u_content_button'  => $buttonCounter,
            ],
            'body' => [
                'id'      => 'tx_body',
                'rows'    => [$rowHeader, $rowTitle, $rowBody, $rowFooter],
                'headers' => [],
                'footers' => [],
                'values'  => self::bodyValues($accent),
            ],
            'schemaVersion' => 16,
        ];
    }

    /** Build the design as a JSON string (for storing in `data_json`). */
    public static function designJson(array $t): string
    {
        return json_encode(self::design($t), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    // ────────────────────────────────────────────────────────────────
    // Block builders
    // ────────────────────────────────────────────────────────────────

    /** A single full-width row containing the given content blocks. */
    private static function row(string $id, string $bgColor, array $contents): array
    {
        return [
            'id'      => $id,
            'cells'   => [1],
            'columns' => [[
                'id'       => $id . '_col',
                'contents' => $contents,
                'values'   => [
                    '_meta'           => ['htmlID' => 'u_column_' . $id, 'htmlClassNames' => 'u_column'],
                    'border'          => (object) [],
                    'padding'         => '0px',
                    'backgroundColor' => '',
                ],
            ]],
            'values' => [
                'displayCondition'       => null,
                'columns'                => false,
                'backgroundColor'        => '',
                'columnsBackgroundColor' => $bgColor,
                'backgroundImage'        => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'custom', 'position' => 'top-center'],
                'padding'                => '0px',
                'anchor'                 => '',
                'hideDesktop'            => false,
                '_meta'                  => ['htmlID' => 'u_' . $id, 'htmlClassNames' => 'u_row'],
                'selectable'             => true,
                'draggable'              => true,
                'duplicatable'           => true,
                'deletable'              => true,
                'hideable'               => true,
                'hideMobile'             => false,
                'noStackMobile'          => false,
            ],
        ];
    }

    private static function htmlBlock(int $n, string $html): array
    {
        return [
            'id'     => 'html_' . $n,
            'type'   => 'html',
            'values' => [
                'html'             => $html,
                'hideDesktop'      => false,
                'displayCondition' => null,
                'containerPadding' => '0px',
                'anchor'           => '',
                '_meta'            => ['htmlID' => 'u_content_html_' . $n, 'htmlClassNames' => 'u_content_html'],
                'selectable'       => true,
                'draggable'        => true,
                'duplicatable'     => true,
                'deletable'        => true,
                'hideable'         => true,
                'hideMobile'       => false,
            ],
        ];
    }

    private static function textBlock(
        int $n,
        string $text,
        string $containerPadding,
        string $fontSize = '14px',
        string $lineHeight = '150%',
        ?string $color = null
    ): array {
        $values = [
            'containerPadding' => $containerPadding,
            'anchor'           => '',
            'fontSize'         => $fontSize,
            'textAlign'        => 'left',
            'lineHeight'       => $lineHeight,
            'linkStyle'        => ['inherit' => true, 'linkColor' => '#4f46e5', 'linkHoverColor' => '#4f46e5', 'linkUnderline' => true, 'linkHoverUnderline' => true],
            'hideDesktop'      => false,
            'displayCondition' => null,
            '_meta'            => ['htmlID' => 'u_content_text_' . $n, 'htmlClassNames' => 'u_content_text'],
            'selectable'       => true,
            'draggable'        => true,
            'duplicatable'     => true,
            'deletable'        => true,
            'hideable'         => true,
            'hideMobile'       => false,
            'text'             => $text,
        ];
        if ($color !== null) {
            $values['color'] = $color;
        }

        return [
            'id'                       => 'text_' . $n,
            'type'                     => 'text',
            'values'                   => $values,
            'hasDeprecatedFontControls' => true,
        ];
    }

    private static function buttonBlock(int $n, string $text, string $href, string $accent): array
    {
        return [
            'id'     => 'button_' . $n,
            'type'   => 'button',
            'values' => [
                'containerPadding' => '8px 36px 14px',
                'anchor'           => '',
                'href'             => ['name' => 'web', 'values' => ['href' => $href, 'target' => '_blank']],
                'buttonColors'     => ['color' => '#ffffff', 'backgroundColor' => $accent, 'hoverColor' => '#ffffff', 'hoverBackgroundColor' => $accent],
                'size'             => ['autoWidth' => true, 'width' => '100%'],
                'fontSize'         => '14px',
                'textAlign'        => 'left',
                'lineHeight'       => '120%',
                'padding'          => '13px 26px',
                'border'           => (object) [],
                'borderRadius'     => '8px',
                'hideDesktop'      => false,
                'displayCondition' => null,
                '_meta'            => ['htmlID' => 'u_content_button_' . $n, 'htmlClassNames' => 'u_content_button'],
                'selectable'       => true,
                'draggable'        => true,
                'duplicatable'     => true,
                'deletable'        => true,
                'hideable'         => true,
                'hideMobile'       => false,
                'text'             => '<strong>' . $text . '</strong>',
            ],
            'hasDeprecatedFontControls' => true,
        ];
    }

    private static function bodyValues(string $accent): array
    {
        return [
            'popupPosition'                     => 'center',
            'popupWidth'                        => '600px',
            'popupHeight'                       => 'auto',
            'borderRadius'                      => '10px',
            'contentAlign'                      => 'center',
            'contentVerticalAlign'              => 'center',
            'contentWidth'                      => '600px',
            'fontFamily'                        => ['label' => 'Arial', 'value' => 'arial,helvetica,sans-serif'],
            'textColor'                         => '#3d4452',
            'popupBackgroundColor'              => '#FFFFFF',
            'popupBackgroundImage'              => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'cover', 'position' => 'center'],
            'popupOverlay_backgroundColor'      => 'rgba(0, 0, 0, 0.1)',
            'popupCloseButton_position'         => 'top-right',
            'popupCloseButton_backgroundColor'  => '#DDDDDD',
            'popupCloseButton_iconColor'        => '#000000',
            'popupCloseButton_borderRadius'     => '0px',
            'popupCloseButton_margin'           => '0px',
            'popupCloseButton_action'           => ['name' => 'close_popup', 'attrs' => ['onClick' => "document.querySelector('.u-popup-container').style.display = 'none';"]],
            'backgroundColor'                   => '#eef1f6',
            'preheaderText'                     => '',
            'linkStyle'                         => ['body' => true, 'linkColor' => $accent, 'linkHoverColor' => $accent, 'linkUnderline' => true, 'linkHoverUnderline' => true],
            'backgroundImage'                   => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'custom', 'position' => 'top-center'],
            '_meta'                             => ['htmlID' => 'u_body', 'htmlClassNames' => 'u_body'],
        ];
    }
}
