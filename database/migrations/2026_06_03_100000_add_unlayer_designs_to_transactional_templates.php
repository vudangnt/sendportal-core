<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Give the 6 default transactional templates (job-application statuses) a valid
 * Unlayer `data_json` design + matching `content` HTML so they open correctly
 * in the drag-and-drop editor (which renders from data_json, not raw HTML).
 *
 * Applies to:
 *   - global defaults (workspace_id IS NULL, is_default = true)
 *   - workspace copies (workspace_id NOT NULL) that were seeded but never
 *     edited in Unlayer (data_json IS NULL) — so we never clobber a design a
 *     user already built.
 *
 * Variables (rendered server-side via {{ var }}): candidate_name, job_title,
 * company. Optional contextual ones degrade to empty string when absent:
 * interview_date, interview_link, offer_url, start_date, recruiter_name.
 */
class AddUnlayerDesignsToTransactionalTemplates extends Migration
{
    public function up(): void
    {
        foreach ($this->templates() as $code => $tpl) {
            $design = json_encode($this->buildDesign($tpl), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $content = $this->buildHtml($tpl);

            DB::table('sendportal_templates')
                ->where('kind', 'transactional')
                ->where('code', $code)
                ->whereNull('data_json')
                ->update([
                    'name'       => $tpl['name'],
                    'subject'    => $tpl['subject'],
                    'content'    => $content,
                    'data_json'  => $design,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Clear only the design we added; leave name/subject/content in place.
        DB::table('sendportal_templates')
            ->where('kind', 'transactional')
            ->whereIn('code', array_keys($this->templates()))
            ->update(['data_json' => null]);
    }

    /**
     * Per-status content. `body` = array of HTML paragraph strings (may contain
     * {{ var }} placeholders). `button` optional [text, href].
     */
    private function templates(): array
    {
        return [
            'applied' => [
                'name'    => 'Application received',
                'subject' => 'Application received: {{ job_title }}',
                'color'   => '#2563eb',
                'title'   => 'Application Received',
                'body'    => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for applying for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>. We have received your application and our recruitment team will review it carefully.',
                    'We will get back to you with the next steps as soon as possible. Thank you for your interest in joining us.',
                ],
                'button'  => null,
                'footer'  => 'You are receiving this email because you applied for a position at {{ company }}.',
            ],

            'shortlist' => [
                'name'    => 'Shortlisted',
                'subject' => "You've been shortlisted for {{ job_title }}",
                'color'   => '#16a34a',
                'title'   => "You've Been Shortlisted",
                'body'    => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Great news — you have been <strong>shortlisted</strong> for the <strong>{{ job_title }}</strong> role at <strong>{{ company }}</strong>.',
                    'Our team was impressed with your profile. We will reach out shortly with details about the next stage of the process.',
                ],
                'button'  => null,
                'footer'  => 'Sent by the recruitment team at {{ company }}.',
            ],

            'interviewed' => [
                'name'    => 'Interview confirmation',
                'subject' => 'Interview scheduled: {{ job_title }}',
                'color'   => '#4f46e5',
                'title'   => 'Interview Scheduled',
                'body'    => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your interview for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                    'Please use the button below to join or view the details. If the time does not work for you, reply to this email and we will reschedule.',
                ],
                'button'  => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
                'footer'  => 'Sent by the recruitment team at {{ company }}.',
            ],

            'offered' => [
                'name'    => 'Job offer',
                'subject' => 'Job offer — {{ company }}',
                'color'   => '#7c3aed',
                'title'   => 'Congratulations!',
                'body'    => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We are delighted to offer you the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'Please review your offer details using the button below. We are excited about the possibility of you joining our team.',
                ],
                'button'  => ['text' => 'View Your Offer', 'href' => '{{ offer_url }}'],
                'footer'  => 'Sent by the recruitment team at {{ company }}.',
            ],

            'onboard_probation' => [
                'name'    => 'Onboarding & probation',
                'subject' => 'Welcome aboard, {{ candidate_name }}',
                'color'   => '#0d9488',
                'title'   => 'Welcome Aboard',
                'body'    => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Welcome to <strong>{{ company }}</strong>! We are thrilled to have you join us as <strong>{{ job_title }}</strong>.',
                    '<strong>Start date:</strong> {{ start_date }}',
                    'Your onboarding plan and probation details will follow shortly. If you have any questions before your first day, just reply to this email.',
                ],
                'button'  => null,
                'footer'  => 'Sent by the People team at {{ company }}.',
            ],

            'fail' => [
                'name'    => 'Application result',
                'subject' => 'Application status — {{ job_title }}',
                'color'   => '#475569',
                'title'   => 'Application Update',
                'body'    => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for taking the time to apply for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong> and for sharing your experience with us.',
                    'After careful consideration, we have decided not to move forward with your application at this time. This was a difficult decision given the strength of applicants.',
                    'We genuinely appreciate your interest and encourage you to apply for future openings that match your skills. We wish you the very best in your search.',
                ],
                'button'  => null,
                'footer'  => 'Sent by the recruitment team at {{ company }}.',
            ],
        ];
    }

    // ── HTML (what the API actually sends) ──────────────────────────────────

    private function buildHtml(array $t): string
    {
        $color  = $t['color'];
        $title  = $t['title'];
        $bodyHtml = '';
        foreach ($t['body'] as $p) {
            $bodyHtml .= '<p style="margin:0 0 14px;">' . $p . '</p>';
        }

        $buttonHtml = '';
        if (!empty($t['button'])) {
            $buttonHtml =
                '<table cellpadding="0" cellspacing="0" style="margin:24px 0 4px;"><tr><td '
                . 'style="background:' . $color . ';border-radius:6px;">'
                . '<a href="' . $t['button']['href'] . '" target="_blank" '
                . 'style="display:inline-block;padding:13px 28px;color:#ffffff;font-size:15px;'
                . 'font-weight:bold;text-decoration:none;font-family:Arial,sans-serif;">'
                . $t['button']['text'] . '</a></td></tr></table>';
        }

        return
            '<div style="background:#f0f1f5;padding:24px 0;">'
            . '<table width="600" align="center" cellpadding="0" cellspacing="0" '
            . 'style="margin:0 auto;background:#ffffff;border-radius:10px;overflow:hidden;'
            . 'font-family:Arial,Helvetica,sans-serif;box-shadow:0 1px 4px rgba(0,0,0,0.06);">'
            . '<tr><td style="background:' . $color . ';padding:34px 40px;text-align:center;">'
            . '<span style="color:#ffffff;font-size:26px;font-weight:bold;letter-spacing:.2px;">'
            . $title . '</span></td></tr>'
            . '<tr><td style="padding:32px 40px;color:#333333;font-size:15px;line-height:1.7;">'
            . $bodyHtml . $buttonHtml . '</td></tr>'
            . '<tr><td style="background:#f7f8fa;padding:20px 40px;text-align:center;'
            . 'color:#9aa1ad;font-size:12px;line-height:1.6;">' . $t['footer'] . '</td></tr>'
            . '</table></div>';
    }

    // ── Unlayer design (what the drag-and-drop editor loads) ────────────────

    private int $textCounter = 0;
    private int $btnCounter = 0;

    private function buildDesign(array $t): array
    {
        $this->textCounter = 0;
        $this->btnCounter = 0;
        $color = $t['color'];

        $rows = [];

        // Header band
        $rows[] = $this->row($color, [
            $this->text(
                '<p style="font-size: 14px; line-height: 140%;"><span style="font-size: 26px; line-height: 36.4px;">'
                . '<strong>' . $t['title'] . '</strong></span></p>',
                ['color' => '#ffffff', 'textAlign' => 'center', 'containerPadding' => '34px 40px']
            ),
        ]);

        // Body
        $bodyText = '';
        foreach ($t['body'] as $p) {
            $bodyText .= '<p style="font-size: 15px; line-height: 170%;">' . $p . '</p>';
        }
        $bodyContents = [
            $this->text($bodyText, ['color' => '#333333', 'textAlign' => 'left', 'containerPadding' => '32px 40px 10px']),
        ];
        if (!empty($t['button'])) {
            $bodyContents[] = $this->button($color, $t['button']['text'], $t['button']['href']);
        }
        $rows[] = $this->row('#ffffff', $bodyContents);

        // Footer
        $rows[] = $this->row('#f7f8fa', [
            $this->text(
                '<p style="font-size: 12px; line-height: 160%;">' . $t['footer'] . '</p>',
                ['color' => '#9aa1ad', 'textAlign' => 'center', 'containerPadding' => '20px 40px']
            ),
        ]);

        return [
            'counters' => [
                'u_row'             => 3,
                'u_column'          => 3,
                'u_content_text'    => $this->textCounter,
                'u_content_button'  => $this->btnCounter,
            ],
            'body' => [
                'id'      => 'tpl_body',
                'rows'    => $rows,
                'headers' => [],
                'footers' => [],
                'values'  => $this->bodyValues($color),
            ],
            'schemaVersion' => 16,
        ];
    }

    private function row(string $bandColor, array $contents): array
    {
        static $rowId = 0;
        $rowId++;

        return [
            'id'      => 'row_' . $rowId,
            'cells'   => [1],
            'columns' => [[
                'id'       => 'col_' . $rowId,
                'contents' => $contents,
                'values'   => [
                    '_meta' => ['htmlID' => 'u_column_' . $rowId, 'htmlClassNames' => 'u_column'],
                    'border' => (object) [], 'padding' => '0px', 'backgroundColor' => '',
                ],
            ]],
            'values' => [
                'displayCondition' => null, 'columns' => false, 'backgroundColor' => '',
                'columnsBackgroundColor' => $bandColor,
                'backgroundImage' => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'custom', 'position' => 'top-center'],
                'padding' => '0px', 'anchor' => '', 'hideDesktop' => false,
                '_meta' => ['htmlID' => 'u_row_' . $rowId, 'htmlClassNames' => 'u_row'],
                'selectable' => true, 'draggable' => true, 'duplicatable' => true,
                'deletable' => true, 'hideable' => true, 'hideMobile' => false, 'noStackMobile' => false,
            ],
        ];
    }

    private function text(string $html, array $opts): array
    {
        $this->textCounter++;
        $n = $this->textCounter;

        return [
            'id'   => 'text_' . $n,
            'type' => 'text',
            'values' => [
                'containerPadding' => $opts['containerPadding'] ?? '20px 40px',
                'anchor' => '',
                'fontSize' => '14px',
                'color' => $opts['color'] ?? '#333333',
                'textAlign' => $opts['textAlign'] ?? 'left',
                'lineHeight' => '170%',
                'linkStyle' => ['inherit' => true, 'linkColor' => '#0000ee', 'linkHoverColor' => '#0000ee', 'linkUnderline' => true, 'linkHoverUnderline' => true],
                'hideDesktop' => false,
                'displayCondition' => null,
                '_meta' => ['htmlID' => 'u_content_text_' . $n, 'htmlClassNames' => 'u_content_text'],
                'selectable' => true, 'draggable' => true, 'duplicatable' => true,
                'deletable' => true, 'hideable' => true, 'hideMobile' => false,
                'text' => $html,
            ],
            'hasDeprecatedFontControls' => true,
        ];
    }

    private function button(string $color, string $label, string $href): array
    {
        $this->btnCounter++;
        $n = $this->btnCounter;

        return [
            'id'   => 'btn_' . $n,
            'type' => 'button',
            'values' => [
                'containerPadding' => '4px 40px 32px',
                'anchor' => '',
                'href' => ['name' => 'web', 'values' => ['href' => $href, 'target' => '_blank']],
                'buttonColors' => ['color' => '#ffffff', 'backgroundColor' => $color, 'hoverColor' => '#ffffff', 'hoverBackgroundColor' => $color],
                'size' => ['autoWidth' => true, 'width' => '100%'],
                'fontSize' => '15px',
                'textAlign' => 'left',
                'lineHeight' => '120%',
                'padding' => '13px 28px',
                'border' => (object) [],
                'borderRadius' => '6px',
                'hideDesktop' => false,
                'displayCondition' => null,
                '_meta' => ['htmlID' => 'u_content_button_' . $n, 'htmlClassNames' => 'u_content_button'],
                'selectable' => true, 'draggable' => true, 'duplicatable' => true,
                'deletable' => true, 'hideable' => true, 'hideMobile' => false,
                'text' => '<strong>' . $label . '</strong>',
                'calculatedWidth' => 180, 'calculatedHeight' => 45,
            ],
            'hasDeprecatedFontControls' => true,
        ];
    }

    private function bodyValues(string $color): array
    {
        return [
            'popupPosition' => 'center', 'popupWidth' => '600px', 'popupHeight' => 'auto',
            'borderRadius' => '10px', 'contentAlign' => 'center', 'contentVerticalAlign' => 'center',
            'contentWidth' => '600px',
            'fontFamily' => ['label' => 'Arial', 'value' => 'arial,helvetica,sans-serif'],
            'textColor' => '#333333', 'popupBackgroundColor' => '#FFFFFF',
            'popupBackgroundImage' => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'cover', 'position' => 'center'],
            'popupOverlay_backgroundColor' => 'rgba(0, 0, 0, 0.1)',
            'popupCloseButton_position' => 'top-right', 'popupCloseButton_backgroundColor' => '#DDDDDD',
            'popupCloseButton_iconColor' => '#000000', 'popupCloseButton_borderRadius' => '0px',
            'popupCloseButton_margin' => '0px',
            'popupCloseButton_action' => ['name' => 'close_popup', 'attrs' => ['onClick' => "document.querySelector('.u-popup-container').style.display = 'none';"]],
            'backgroundColor' => '#f0f1f5', 'preheaderText' => '',
            'linkStyle' => ['body' => true, 'linkColor' => $color, 'linkHoverColor' => $color, 'linkUnderline' => true, 'linkHoverUnderline' => true],
            'backgroundImage' => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'custom', 'position' => 'top-center'],
            '_meta' => ['htmlID' => 'u_body', 'htmlClassNames' => 'u_body'],
        ];
    }
}
