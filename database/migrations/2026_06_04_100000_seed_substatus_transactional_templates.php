<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed the 24 job-application SUB-statuses (children of the 6 parents) from the
 * Lark ATS as Unlayer-compatible transactional templates, in addition to the 6
 * parents seeded by 2026_06_03_100000.
 *
 * Source (Lark "Job Application Status", embedded per-parent sheets):
 *   applied:           send to client, waiting
 *   shortlist:         chon_lam_bai_test, chon_phong_van
 *   interviewed:       interviewed_r1, interviewed_r2, inteviewed_r3, assessment
 *   offered:           sent_offer, offer_accepted
 *   onboard_probation: onboarding_first, more_time_probation, pass probation
 *   fail:              candidate_withdraw, reject_offer, employer_cancel_offer,
 *                      not_meet_requirement, fail_interview, fail_test,
 *                      close_job, trung_application, fail_probation
 *
 * Codes are stored EXACTLY as in Lark (incl. spaces in "send to client" and
 * "pass probation") so they match what the recruiter app sends to the API.
 *
 * Each code is inserted as a global default (workspace_id NULL, is_default
 * true) and backfilled into every existing workspace (firstOrCreate, so no
 * clobbering). New workspaces get them via the Workspace::created observer.
 */
class SeedSubstatusTransactionalTemplates extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->templates() as $code => $tpl) {
            $design  = json_encode($this->buildDesign($tpl), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $content = $this->buildHtml($tpl);

            // 1) Global default
            DB::table('sendportal_templates')->updateOrInsert(
                ['workspace_id' => null, 'code' => $code, 'kind' => 'transactional'],
                [
                    'name'       => $tpl['name'],
                    'subject'    => $tpl['subject'],
                    'content'    => $content,
                    'data_json'  => $design,
                    'is_default' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            // 2) Backfill every existing workspace (skip if a copy already exists)
            DB::table('workspaces')->orderBy('id')->pluck('id')->each(function ($wsId) use ($code, $tpl, $content, $design, $now) {
                $exists = DB::table('sendportal_templates')
                    ->where('workspace_id', $wsId)
                    ->where('code', $code)
                    ->where('kind', 'transactional')
                    ->exists();
                if ($exists) {
                    return;
                }
                DB::table('sendportal_templates')->insert([
                    'workspace_id' => $wsId,
                    'code'         => $code,
                    'kind'         => 'transactional',
                    'name'         => $tpl['name'],
                    'subject'      => $tpl['subject'],
                    'content'      => $content,
                    'data_json'    => $design,
                    'is_default'   => false,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            });
        }
    }

    public function down(): void
    {
        DB::table('sendportal_templates')
            ->where('kind', 'transactional')
            ->whereIn('code', array_keys($this->templates()))
            ->delete();
    }

    /**
     * 24 sub-status templates. Color is grouped by parent status.
     */
    private function templates(): array
    {
        $BLUE = '#2563eb';   // applied
        $GREEN = '#16a34a';  // shortlist
        $INDIGO = '#4f46e5'; // interviewed
        $PURPLE = '#7c3aed'; // offered
        $TEAL = '#0d9488';   // onboard_probation
        $SLATE = '#475569';  // fail

        return [
            // ── applied ────────────────────────────────────────────────
            'send to client' => [
                'name' => 'Candidate submitted to client', 'color' => $BLUE,
                'subject' => 'Candidate submitted for {{ job_title }}',
                'title' => 'Candidate Submitted',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your profile has been submitted to <strong>{{ company }}</strong> for the <strong>{{ job_title }}</strong> position. The hiring team will review it and we will keep you updated on their decision.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'waiting' => [
                'name' => 'Awaiting employer review', 'color' => $BLUE,
                'subject' => 'Your application for {{ job_title }} is under review',
                'title' => 'Application Under Review',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your application for <strong>{{ job_title }}</strong> is currently being reviewed by <strong>{{ company }}</strong>. We appreciate your patience and will notify you as soon as there is an update.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],

            // ── shortlist ──────────────────────────────────────────────
            'chon_lam_bai_test' => [
                'name' => 'Selected for test', 'color' => $GREEN,
                'subject' => 'Test invitation: {{ job_title }}',
                'title' => 'You Have Been Selected for a Test',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Congratulations! You have been selected to complete a test for the <strong>{{ job_title }}</strong> role at <strong>{{ company }}</strong>.',
                    'Please use the button below to start. If you have any questions, simply reply to this email.',
                ],
                'button' => ['text' => 'Start the Test', 'href' => '{{ test_link }}'],
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'chon_phong_van' => [
                'name' => 'Selected for interview', 'color' => $GREEN,
                'subject' => "You've been selected for an interview: {{ job_title }}",
                'title' => 'Selected for Interview',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Good news — you have been selected for an interview for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'We will reach out shortly with the schedule and details.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],

            // ── interviewed ────────────────────────────────────────────
            'interviewed_r1' => [
                'name' => 'Interview round 1', 'color' => $INDIGO,
                'subject' => 'Interview (Round 1): {{ job_title }}',
                'title' => 'Interview — Round 1',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your <strong>Round 1</strong> interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                ],
                'button' => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'interviewed_r2' => [
                'name' => 'Interview round 2', 'color' => $INDIGO,
                'subject' => 'Interview (Round 2): {{ job_title }}',
                'title' => 'Interview — Round 2',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your <strong>Round 2</strong> interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                ],
                'button' => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'inteviewed_r3' => [ // code spelled exactly as in Lark (missing 'r')
                'name' => 'Interview round 3', 'color' => $INDIGO,
                'subject' => 'Interview (Round 3): {{ job_title }}',
                'title' => 'Interview — Round 3',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your <strong>Round 3</strong> interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                ],
                'button' => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'assessment' => [
                'name' => 'Assessment', 'color' => $INDIGO,
                'subject' => 'Assessment for {{ job_title }}',
                'title' => 'Assessment Stage',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'As part of the process for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>, you have been invited to complete an assessment.',
                    'Please use the button below to begin.',
                ],
                'button' => ['text' => 'Start Assessment', 'href' => '{{ assessment_link }}'],
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],

            // ── offered ────────────────────────────────────────────────
            'sent_offer' => [
                'name' => 'Offer sent', 'color' => $PURPLE,
                'subject' => 'Your offer from {{ company }}',
                'title' => 'Your Offer Is Ready',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We are pleased to share your offer for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'Please review the details using the button below.',
                ],
                'button' => ['text' => 'View Your Offer', 'href' => '{{ offer_url }}'],
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'offer_accepted' => [
                'name' => 'Offer accepted', 'color' => $PURPLE,
                'subject' => 'Offer accepted — welcome to {{ company }}',
                'title' => 'Offer Accepted',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for accepting the offer for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>. We are thrilled to have you join us!',
                    'Our team will be in touch shortly with your onboarding details.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],

            // ── onboard_probation ──────────────────────────────────────
            'onboarding_first' => [
                'name' => 'Onboarding', 'color' => $TEAL,
                'subject' => 'Onboarding details — {{ company }}',
                'title' => 'Welcome — Let\'s Get You Onboarded',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Welcome to <strong>{{ company }}</strong>! We are excited for your first day as <strong>{{ job_title }}</strong>.',
                    '<strong>Start date:</strong> {{ start_date }}',
                    'Your onboarding schedule will follow shortly. If you have any questions before you begin, just reply to this email.',
                ],
                'button' => null,
                'footer' => 'Sent by the People team at {{ company }}.',
            ],
            'more_time_probation' => [
                'name' => 'Probation extended', 'color' => $TEAL,
                'subject' => 'Update on your probation — {{ job_title }}',
                'title' => 'Probation Period Extended',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'After reviewing your progress as <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>, we have decided to extend your probation period to give you more time to settle in.',
                    'Your manager will share specific goals and a timeline with you. We are here to support you.',
                ],
                'button' => null,
                'footer' => 'Sent by the People team at {{ company }}.',
            ],
            'pass probation' => [
                'name' => 'Passed probation', 'color' => $TEAL,
                'subject' => 'Congratulations — you passed probation!',
                'title' => 'You Passed Probation',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Congratulations! You have successfully passed your probation period as <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'We are delighted to confirm your continued role with us. Keep up the great work!',
                ],
                'button' => null,
                'footer' => 'Sent by the People team at {{ company }}.',
            ],

            // ── fail (neutral / respectful) ────────────────────────────
            'candidate_withdraw' => [
                'name' => 'Candidate withdrew', 'color' => $SLATE,
                'subject' => 'Withdrawal confirmed — {{ job_title }}',
                'title' => 'Application Withdrawn',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We have recorded your decision to withdraw from the <strong>{{ job_title }}</strong> process at <strong>{{ company }}</strong>.',
                    'Thank you for letting us know. You are always welcome to apply again in the future.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'reject_offer' => [
                'name' => 'Offer declined', 'color' => $SLATE,
                'subject' => 'Offer declined — {{ job_title }}',
                'title' => 'Offer Declined',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We have noted that you have declined the offer for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'We respect your decision and wish you the very best. We hope our paths cross again.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'employer_cancel_offer' => [
                'name' => 'Offer withdrawn by employer', 'color' => $SLATE,
                'subject' => 'Update regarding your offer — {{ job_title }}',
                'title' => 'Offer Update',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We regret to inform you that the offer for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been withdrawn due to changes on the employer side.',
                    'We sincerely apologise for any inconvenience and would be glad to consider you for other suitable opportunities.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'not_meet_requirement' => [
                'name' => 'Did not meet requirements', 'color' => $SLATE,
                'subject' => 'Application status — {{ job_title }}',
                'title' => 'Application Update',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for your interest in the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'After careful review, your profile did not fully match the requirements for this role, so we will not be moving forward at this time. We encourage you to apply for future openings that fit your experience.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'fail_interview' => [
                'name' => 'Unsuccessful after interview', 'color' => $SLATE,
                'subject' => 'Interview result — {{ job_title }}',
                'title' => 'Interview Result',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for taking the time to interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'After careful consideration, we have decided not to proceed with your application at this stage. We genuinely appreciated the opportunity to speak with you and wish you success in your search.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'fail_test' => [
                'name' => 'Unsuccessful after test', 'color' => $SLATE,
                'subject' => 'Test result — {{ job_title }}',
                'title' => 'Test Result',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for completing the test for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'Unfortunately, your results did not meet the threshold for this role, so we will not be moving forward at this time. We appreciate your effort and wish you all the best.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'close_job' => [
                'name' => 'Position closed', 'color' => $SLATE,
                'subject' => 'Update on {{ job_title }} at {{ company }}',
                'title' => 'Position Closed',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for your interest in <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'This position has now been closed, so we are unable to progress your application further. We will keep your profile on file and reach out should a suitable role open up.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'trung_application' => [
                'name' => 'Duplicate application', 'color' => $SLATE,
                'subject' => 'Your application for {{ job_title }}',
                'title' => 'Duplicate Application',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We noticed you already have an active application for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'We have kept your original application, so there is nothing further you need to do. Thank you for your continued interest.',
                ],
                'button' => null,
                'footer' => 'Sent by the recruitment team at {{ company }}.',
            ],
            'fail_probation' => [
                'name' => 'Did not pass probation', 'color' => $SLATE,
                'subject' => 'Probation result — {{ job_title }}',
                'title' => 'Probation Result',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for your efforts during your probation period as <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'After careful review, we have concluded that the role is not the right fit at this time, and your probation will not be confirmed. We wish you the very best going forward.',
                ],
                'button' => null,
                'footer' => 'Sent by the People team at {{ company }}.',
            ],
        ];
    }

    // ── HTML render (API send body) ─────────────────────────────────────────

    private function buildHtml(array $t): string
    {
        $color = $t['color'];
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
            . $t['title'] . '</span></td></tr>'
            . '<tr><td style="padding:32px 40px;color:#333333;font-size:15px;line-height:1.7;">'
            . $bodyHtml . $buttonHtml . '</td></tr>'
            . '<tr><td style="background:#f7f8fa;padding:20px 40px;text-align:center;'
            . 'color:#9aa1ad;font-size:12px;line-height:1.6;">' . $t['footer'] . '</td></tr>'
            . '</table></div>';
    }

    // ── Unlayer design (editor) ─────────────────────────────────────────────

    private int $textCounter = 0;
    private int $btnCounter = 0;
    private int $rowCounter = 0;

    private function buildDesign(array $t): array
    {
        $this->textCounter = 0;
        $this->btnCounter = 0;
        $this->rowCounter = 0;
        $color = $t['color'];

        $rows = [];

        $rows[] = $this->row($color, [
            $this->text(
                '<p style="font-size: 14px; line-height: 140%;"><span style="font-size: 26px; line-height: 36.4px;">'
                . '<strong>' . $t['title'] . '</strong></span></p>',
                ['color' => '#ffffff', 'textAlign' => 'center', 'containerPadding' => '34px 40px']
            ),
        ]);

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

        $rows[] = $this->row('#f7f8fa', [
            $this->text(
                '<p style="font-size: 12px; line-height: 160%;">' . $t['footer'] . '</p>',
                ['color' => '#9aa1ad', 'textAlign' => 'center', 'containerPadding' => '20px 40px']
            ),
        ]);

        return [
            'counters' => [
                'u_row' => $this->rowCounter, 'u_column' => $this->rowCounter,
                'u_content_text' => $this->textCounter, 'u_content_button' => $this->btnCounter,
            ],
            'body' => [
                'id' => 'tpl_body', 'rows' => $rows, 'headers' => [], 'footers' => [],
                'values' => $this->bodyValues($color),
            ],
            'schemaVersion' => 16,
        ];
    }

    private function row(string $bandColor, array $contents): array
    {
        $this->rowCounter++;
        $r = $this->rowCounter;

        return [
            'id' => 'row_' . $r, 'cells' => [1],
            'columns' => [[
                'id' => 'col_' . $r, 'contents' => $contents,
                'values' => [
                    '_meta' => ['htmlID' => 'u_column_' . $r, 'htmlClassNames' => 'u_column'],
                    'border' => (object) [], 'padding' => '0px', 'backgroundColor' => '',
                ],
            ]],
            'values' => [
                'displayCondition' => null, 'columns' => false, 'backgroundColor' => '',
                'columnsBackgroundColor' => $bandColor,
                'backgroundImage' => ['url' => '', 'fullWidth' => true, 'repeat' => 'no-repeat', 'size' => 'custom', 'position' => 'top-center'],
                'padding' => '0px', 'anchor' => '', 'hideDesktop' => false,
                '_meta' => ['htmlID' => 'u_row_' . $r, 'htmlClassNames' => 'u_row'],
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
            'id' => 'text_' . $n, 'type' => 'text',
            'values' => [
                'containerPadding' => $opts['containerPadding'] ?? '20px 40px',
                'anchor' => '', 'fontSize' => '14px', 'color' => $opts['color'] ?? '#333333',
                'textAlign' => $opts['textAlign'] ?? 'left', 'lineHeight' => '170%',
                'linkStyle' => ['inherit' => true, 'linkColor' => '#0000ee', 'linkHoverColor' => '#0000ee', 'linkUnderline' => true, 'linkHoverUnderline' => true],
                'hideDesktop' => false, 'displayCondition' => null,
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
            'id' => 'btn_' . $n, 'type' => 'button',
            'values' => [
                'containerPadding' => '4px 40px 32px', 'anchor' => '',
                'href' => ['name' => 'web', 'values' => ['href' => $href, 'target' => '_blank']],
                'buttonColors' => ['color' => '#ffffff', 'backgroundColor' => $color, 'hoverColor' => '#ffffff', 'hoverBackgroundColor' => $color],
                'size' => ['autoWidth' => true, 'width' => '100%'],
                'fontSize' => '15px', 'textAlign' => 'left', 'lineHeight' => '120%',
                'padding' => '13px 28px', 'border' => (object) [], 'borderRadius' => '6px',
                'hideDesktop' => false, 'displayCondition' => null,
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
