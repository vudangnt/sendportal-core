<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Templates;

/**
 * Builds an Unlayer design (schemaVersion 16) that wraps a block of raw HTML in
 * a single Unlayer "html" content block.
 *
 * Transactional templates are authored as hand-crafted, email-grade HTML (with
 * {{ merge }} / branding placeholders). The Unlayer visual editor, however,
 * loads its canvas from a design JSON (`data_json`), not from raw `content`.
 * Wrapping the exact content in one HTML block gives the editor a valid design
 * that renders the email as-is — the closest possible match to the stored HTML —
 * so opening a template to edit no longer shows a blank canvas (and Save no
 * longer exports an empty canvas over the content).
 */
class UnlayerDesignBuilder
{
    /** Build the design array for the given HTML. */
    public static function designFromHtml(string $html): array
    {
        return [
            'counters' => ['u_row' => 1, 'u_column' => 1, 'u_content_html' => 1],
            'body' => [
                'id' => 'tpl_body',
                'rows' => [[
                    'id' => 'row_1',
                    'cells' => [1],
                    'columns' => [[
                        'id' => 'col_1',
                        'contents' => [[
                            'id' => 'html_1',
                            'type' => 'html',
                            'values' => [
                                'html' => $html,
                                'hideDesktop' => false,
                                'displayCondition' => null,
                                'containerPadding' => '0px',
                                'anchor' => '',
                                '_meta' => ['htmlID' => 'u_content_html_1', 'htmlClassNames' => 'u_content_html'],
                                'selectable' => true,
                                'draggable' => true,
                                'duplicatable' => true,
                                'deletable' => true,
                                'hideable' => true,
                                'hideMobile' => false,
                            ],
                        ]],
                        'values' => [
                            '_meta' => ['htmlID' => 'u_column_1', 'htmlClassNames' => 'u_column'],
                            'border' => (object) [],
                            'padding' => '0px',
                            'backgroundColor' => '',
                        ],
                    ]],
                    'values' => [
                        'displayCondition' => null,
                        'columns' => false,
                        'backgroundColor' => '',
                        'columnsBackgroundColor' => '',
                        'backgroundImage' => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'custom', 'position' => 'top-center'],
                        'padding' => '0px',
                        'anchor' => '',
                        'hideDesktop' => false,
                        '_meta' => ['htmlID' => 'u_row_1', 'htmlClassNames' => 'u_row'],
                        'selectable' => true,
                        'draggable' => true,
                        'duplicatable' => true,
                        'deletable' => true,
                        'hideable' => true,
                        'hideMobile' => false,
                        'noStackMobile' => false,
                    ],
                ]],
                'headers' => [],
                'footers' => [],
                'values' => [
                    'popupPosition' => 'center',
                    'popupWidth' => '600px',
                    'popupHeight' => 'auto',
                    'borderRadius' => '10px',
                    'contentAlign' => 'center',
                    'contentVerticalAlign' => 'center',
                    'contentWidth' => '640px',
                    'fontFamily' => ['label' => 'Arial', 'value' => 'arial,helvetica,sans-serif'],
                    'textColor' => '#333333',
                    'popupBackgroundColor' => '#FFFFFF',
                    'popupBackgroundImage' => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'cover', 'position' => 'center'],
                    'popupOverlay_backgroundColor' => 'rgba(0, 0, 0, 0.1)',
                    'popupCloseButton_position' => 'top-right',
                    'popupCloseButton_backgroundColor' => '#DDDDDD',
                    'popupCloseButton_iconColor' => '#000000',
                    'popupCloseButton_borderRadius' => '0px',
                    'popupCloseButton_margin' => '0px',
                    'popupCloseButton_action' => ['name' => 'close_popup', 'attrs' => ['onClick' => "document.querySelector('.u-popup-container').style.display = 'none';"]],
                    'backgroundColor' => '#eef1f6',
                    'preheaderText' => '',
                    'linkStyle' => ['body' => true, 'linkColor' => '#4f46e5', 'linkHoverColor' => '#4f46e5', 'linkUnderline' => true, 'linkHoverUnderline' => true],
                    'backgroundImage' => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'custom', 'position' => 'top-center'],
                    '_meta' => ['htmlID' => 'u_body', 'htmlClassNames' => 'u_body'],
                ],
            ],
            'schemaVersion' => 16,
        ];
    }

    /** Build the design as a JSON string (for storing in `data_json`). */
    public static function fromHtml(string $html): string
    {
        return json_encode(
            static::designFromHtml($html),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
