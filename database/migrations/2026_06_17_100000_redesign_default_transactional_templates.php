<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Redesign all default transactional templates to the "Accent band" shell:
 * white header with {{ brand_header_html }} + status-colored top border + badge,
 * body (preserved copy) + optional CTA + info, and a branded footer
 * ({{ brand_name }}, {{ brand_contact_html }}, {{ brand_social_html }}).
 *
 * - Updates global defaults (workspace_id NULL) by code; nulls data_json.
 * - Refreshes workspace copies whose content is byte-identical to the OLD
 *   default (i.e. untouched auto-backfills); leaves customized copies alone.
 * - Idempotent. down() is a no-op (pre-deploy DB backup is the safety net).
 */
class RedesignDefaultTransactionalTemplates extends Migration
{
    public function up(): void
    {
        $now = now();

        // Snapshot all current global-default contents in one query (used to
        // detect untouched workspace copies before we overwrite the defaults).
        $oldDefaults = DB::table('sendportal_templates')
            ->whereNull('workspace_id')->where('kind', 'transactional')
            ->whereIn('code', array_keys($this->templates()))
            ->pluck('content', 'code');

        foreach ($this->templates() as $code => $t) {
            $newContent = $this->buildHtml($t);
            $oldDefault = $oldDefaults[$code] ?? null;

            // Refresh workspace copies that are byte-identical to the old default
            // (untouched auto-backfills); customized copies are left alone.
            if ($oldDefault !== null) {
                DB::table('sendportal_templates')
                    ->whereNotNull('workspace_id')->where('kind', 'transactional')->where('code', $code)
                    ->where('content', $oldDefault)
                    ->update(['content' => $newContent, 'data_json' => null, 'updated_at' => $now]);
            }

            DB::table('sendportal_templates')
                ->whereNull('workspace_id')->where('kind', 'transactional')->where('code', $code)
                ->update(['content' => $newContent, 'data_json' => null, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        // No-op: previous content/design is not restorable from here.
        // Restore from the pre-deploy DB backup if needed.
    }

    private function tint(string $accent): string
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

    private function buildHtml(array $t): string
    {
        $accent = $t['color'];
        $tint   = $this->tint($accent);
        $badge  = $t['name'];
        $title  = $t['title'];

        $bodyHtml = '';
        foreach ($t['body'] as $p) {
            $bodyHtml .= '<p style="margin:0 0 14px;">' . $p . '</p>';
        }

        $buttonHtml = '';
        if (!empty($t['button'])) {
            $buttonHtml =
                '<table cellpadding="0" cellspacing="0" style="margin:22px 0 6px;"><tr><td '
                . 'style="background:' . $accent . ';border-radius:8px;">'
                . '<a href="' . $t['button']['href'] . '" target="_blank" '
                . 'style="display:inline-block;padding:13px 26px;color:#ffffff;font-size:14px;'
                . 'font-weight:700;text-decoration:none;font-family:Arial,sans-serif;">'
                . $t['button']['text'] . '</a></td></tr></table>';
        }

        return
            '<div style="background:#eef1f6;padding:24px 12px;">'
            . '<table width="600" align="center" cellpadding="0" cellspacing="0" role="presentation" '
            . 'style="margin:0 auto;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;'
            . 'font-family:-apple-system,Segoe UI,Arial,sans-serif;box-shadow:0 2px 10px rgba(20,30,60,.08);'
            . 'border-top:4px solid ' . $accent . ';">'
            // header (logo or brand name)
            . '<tr><td style="padding:24px 36px 16px;border-bottom:1px solid #eef0f4;">{{ brand_header_html }}</td></tr>'
            // badge + title
            . '<tr><td style="padding:26px 36px 6px;">'
            . '<span style="display:inline-block;background:' . $tint . ';color:' . $accent . ';font-size:11px;'
            . 'font-weight:700;letter-spacing:.6px;text-transform:uppercase;padding:5px 11px;border-radius:20px;">'
            . $badge . '</span>'
            . '<h1 style="margin:14px 0 0;font-size:23px;line-height:1.3;color:#111827;font-weight:700;">' . $title . '</h1>'
            . '</td></tr>'
            // body + cta
            . '<tr><td style="padding:14px 36px 6px;color:#3d4452;font-size:15px;line-height:1.7;">'
            . $bodyHtml . $buttonHtml . '</td></tr>'
            // footer (branding)
            . '<tr><td style="padding:24px 36px 26px;background:#f7f8fa;border-top:1px solid #eef0f4;">'
            . '<div style="font-size:14px;font-weight:700;color:#1f2430;">{{ brand_name }}</div>'
            . '{{ brand_contact_html }}'
            . '{{ brand_social_html }}'
            . '<div style="font-size:11px;color:#aab0bc;margin-top:14px;line-height:1.6;">'
            . '&copy; {{ brand_name }} &middot; You are receiving this email because you applied for a position.</div>'
            . '</td></tr>'
            . '</table></div>';
    }

    /** @return array<string, array> */
    private function templates(): array
    {
        $BLUE = '#2563eb'; $GREEN = '#16a34a'; $INDIGO = '#4f46e5';
        $PURPLE = '#7c3aed'; $TEAL = '#0d9488'; $SLATE = '#475569';

        return [
            // ── parents ────────────────────────────────────────────
            'applied' => [
                'name' => 'Application received', 'color' => $BLUE,
                'subject' => 'Application received: {{ job_title }}', 'title' => 'Application Received',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for applying for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>. We have received your application and our recruitment team will review it carefully.',
                    'We will get back to you with the next steps as soon as possible. Thank you for your interest in joining us.',
                ],
                'button' => null,
            ],
            'shortlist' => [
                'name' => 'Shortlisted', 'color' => $GREEN,
                'subject' => "You've been shortlisted for {{ job_title }}", 'title' => "You've Been Shortlisted",
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Great news — you have been <strong>shortlisted</strong> for the <strong>{{ job_title }}</strong> role at <strong>{{ company }}</strong>.',
                    'Our team was impressed with your profile. We will reach out shortly with details about the next stage of the process.',
                ],
                'button' => null,
            ],
            'interviewed' => [
                'name' => 'Interview confirmation', 'color' => $INDIGO,
                'subject' => 'Interview scheduled: {{ job_title }}', 'title' => 'Interview Scheduled',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your interview for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                    'Please use the button below to join or view the details. If the time does not work for you, reply to this email and we will reschedule.',
                ],
                'button' => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
            ],
            'offered' => [
                'name' => 'Job offer', 'color' => $PURPLE,
                'subject' => 'Job offer — {{ company }}', 'title' => 'Congratulations!',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We are delighted to offer you the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'Please review your offer details using the button below. We are excited about the possibility of you joining our team.',
                ],
                'button' => ['text' => 'View Your Offer', 'href' => '{{ offer_url }}'],
            ],
            'onboard_probation' => [
                'name' => 'Onboarding & probation', 'color' => $TEAL,
                'subject' => 'Welcome aboard, {{ candidate_name }}', 'title' => 'Welcome Aboard',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Welcome to <strong>{{ company }}</strong>! We are thrilled to have you join us as <strong>{{ job_title }}</strong>.',
                    '<strong>Start date:</strong> {{ start_date }}',
                    'Your onboarding plan and probation details will follow shortly. If you have any questions before your first day, just reply to this email.',
                ],
                'button' => null,
            ],
            'fail' => [
                'name' => 'Application result', 'color' => $SLATE,
                'subject' => 'Application status — {{ job_title }}', 'title' => 'Application Update',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for taking the time to apply for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong> and for sharing your experience with us.',
                    'After careful consideration, we have decided not to move forward with your application at this time. This was a difficult decision given the strength of applicants.',
                    'We genuinely appreciate your interest and encourage you to apply for future openings that match your skills. We wish you the very best in your search.',
                ],
                'button' => null,
            ],

            // ── applied sub ────────────────────────────────────────
            'send to client' => [
                'name' => 'Candidate submitted to client', 'color' => $BLUE,
                'subject' => 'Candidate submitted for {{ job_title }}', 'title' => 'Candidate Submitted',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your profile has been submitted to <strong>{{ company }}</strong> for the <strong>{{ job_title }}</strong> position. The hiring team will review it and we will keep you updated on their decision.',
                ],
                'button' => null,
            ],
            'waiting' => [
                'name' => 'Awaiting employer review', 'color' => $BLUE,
                'subject' => 'Your application for {{ job_title }} is under review', 'title' => 'Application Under Review',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your application for <strong>{{ job_title }}</strong> is currently being reviewed by <strong>{{ company }}</strong>. We appreciate your patience and will notify you as soon as there is an update.',
                ],
                'button' => null,
            ],

            // ── shortlist sub ──────────────────────────────────────
            'chon_lam_bai_test' => [
                'name' => 'Selected for test', 'color' => $GREEN,
                'subject' => 'Test invitation: {{ job_title }}', 'title' => 'You Have Been Selected for a Test',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Congratulations! You have been selected to complete a test for the <strong>{{ job_title }}</strong> role at <strong>{{ company }}</strong>.',
                    'Please use the button below to start. If you have any questions, simply reply to this email.',
                ],
                'button' => ['text' => 'Start the Test', 'href' => '{{ test_link }}'],
            ],
            'chon_phong_van' => [
                'name' => 'Selected for interview', 'color' => $GREEN,
                'subject' => "You've been selected for an interview: {{ job_title }}", 'title' => 'Selected for Interview',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Good news — you have been selected for an interview for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'We will reach out shortly with the schedule and details.',
                ],
                'button' => null,
            ],

            // ── interviewed sub ────────────────────────────────────
            'interviewed_r1' => [
                'name' => 'Interview round 1', 'color' => $INDIGO,
                'subject' => 'Interview (Round 1): {{ job_title }}', 'title' => 'Interview — Round 1',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your <strong>Round 1</strong> interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                ],
                'button' => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
            ],
            'interviewed_r2' => [
                'name' => 'Interview round 2', 'color' => $INDIGO,
                'subject' => 'Interview (Round 2): {{ job_title }}', 'title' => 'Interview — Round 2',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your <strong>Round 2</strong> interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                ],
                'button' => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
            ],
            'inteviewed_r3' => [ // code spelled exactly as in production (missing 'r')
                'name' => 'Interview round 3', 'color' => $INDIGO,
                'subject' => 'Interview (Round 3): {{ job_title }}', 'title' => 'Interview — Round 3',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Your <strong>Round 3</strong> interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been scheduled.',
                    '<strong>When:</strong> {{ interview_date }}',
                ],
                'button' => ['text' => 'Join / View Interview', 'href' => '{{ interview_link }}'],
            ],
            'assessment' => [
                'name' => 'Assessment', 'color' => $INDIGO,
                'subject' => 'Assessment for {{ job_title }}', 'title' => 'Assessment Stage',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'As part of the process for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>, you have been invited to complete an assessment.',
                    'Please use the button below to begin.',
                ],
                'button' => ['text' => 'Start Assessment', 'href' => '{{ assessment_link }}'],
            ],

            // ── offered sub ────────────────────────────────────────
            'sent_offer' => [
                'name' => 'Offer sent', 'color' => $PURPLE,
                'subject' => 'Your offer from {{ company }}', 'title' => 'Your Offer Is Ready',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We are pleased to share your offer for the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'Please review the details using the button below.',
                ],
                'button' => ['text' => 'View Your Offer', 'href' => '{{ offer_url }}'],
            ],
            'offer_accepted' => [
                'name' => 'Offer accepted', 'color' => $PURPLE,
                'subject' => 'Offer accepted — welcome to {{ company }}', 'title' => 'Offer Accepted',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for accepting the offer for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>. We are thrilled to have you join us!',
                    'Our team will be in touch shortly with your onboarding details.',
                ],
                'button' => null,
            ],

            // ── onboard sub ────────────────────────────────────────
            'onboarding_first' => [
                'name' => 'Onboarding', 'color' => $TEAL,
                'subject' => 'Onboarding details — {{ company }}', 'title' => "Welcome — Let's Get You Onboarded",
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Welcome to <strong>{{ company }}</strong>! We are excited for your first day as <strong>{{ job_title }}</strong>.',
                    '<strong>Start date:</strong> {{ start_date }}',
                    'Your onboarding schedule will follow shortly. If you have any questions before you begin, just reply to this email.',
                ],
                'button' => null,
            ],
            'more_time_probation' => [
                'name' => 'Probation extended', 'color' => $TEAL,
                'subject' => 'Update on your probation — {{ job_title }}', 'title' => 'Probation Period Extended',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'After reviewing your progress as <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>, we have decided to extend your probation period to give you more time to settle in.',
                    'Your manager will share specific goals and a timeline with you. We are here to support you.',
                ],
                'button' => null,
            ],
            'pass probation' => [
                'name' => 'Passed probation', 'color' => $TEAL,
                'subject' => 'Congratulations — you passed probation!', 'title' => 'You Passed Probation',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Congratulations! You have successfully passed your probation period as <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'We are delighted to confirm your continued role with us. Keep up the great work!',
                ],
                'button' => null,
            ],

            // ── fail sub (neutral / respectful) ────────────────────
            'candidate_withdraw' => [
                'name' => 'Candidate withdrew', 'color' => $SLATE,
                'subject' => 'Withdrawal confirmed — {{ job_title }}', 'title' => 'Application Withdrawn',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We have recorded your decision to withdraw from the <strong>{{ job_title }}</strong> process at <strong>{{ company }}</strong>.',
                    'Thank you for letting us know. You are always welcome to apply again in the future.',
                ],
                'button' => null,
            ],
            'reject_offer' => [
                'name' => 'Offer declined', 'color' => $SLATE,
                'subject' => 'Offer declined — {{ job_title }}', 'title' => 'Offer Declined',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We have noted that you have declined the offer for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'We respect your decision and wish you the very best. We hope our paths cross again.',
                ],
                'button' => null,
            ],
            'employer_cancel_offer' => [
                'name' => 'Offer withdrawn by employer', 'color' => $SLATE,
                'subject' => 'Update regarding your offer — {{ job_title }}', 'title' => 'Offer Update',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We regret to inform you that the offer for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong> has been withdrawn due to changes on the employer side.',
                    'We sincerely apologise for any inconvenience and would be glad to consider you for other suitable opportunities.',
                ],
                'button' => null,
            ],
            'not_meet_requirement' => [
                'name' => 'Did not meet requirements', 'color' => $SLATE,
                'subject' => 'Application status — {{ job_title }}', 'title' => 'Application Update',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for your interest in the <strong>{{ job_title }}</strong> position at <strong>{{ company }}</strong>.',
                    'After careful review, your profile did not fully match the requirements for this role, so we will not be moving forward at this time. We encourage you to apply for future openings that fit your experience.',
                ],
                'button' => null,
            ],
            'fail_interview' => [
                'name' => 'Unsuccessful after interview', 'color' => $SLATE,
                'subject' => 'Interview result — {{ job_title }}', 'title' => 'Interview Result',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for taking the time to interview for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'After careful consideration, we have decided not to proceed with your application at this stage. We genuinely appreciated the opportunity to speak with you and wish you success in your search.',
                ],
                'button' => null,
            ],
            'fail_test' => [
                'name' => 'Unsuccessful after test', 'color' => $SLATE,
                'subject' => 'Test result — {{ job_title }}', 'title' => 'Test Result',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for completing the test for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'Unfortunately, your results did not meet the threshold for this role, so we will not be moving forward at this time. We appreciate your effort and wish you all the best.',
                ],
                'button' => null,
            ],
            'close_job' => [
                'name' => 'Position closed', 'color' => $SLATE,
                'subject' => 'Update on {{ job_title }} at {{ company }}', 'title' => 'Position Closed',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for your interest in <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'This position has now been closed, so we are unable to progress your application further. We will keep your profile on file and reach out should a suitable role open up.',
                ],
                'button' => null,
            ],
            'trung_application' => [
                'name' => 'Duplicate application', 'color' => $SLATE,
                'subject' => 'Your application for {{ job_title }}', 'title' => 'Duplicate Application',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'We noticed you already have an active application for <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'We have kept your original application, so there is nothing further you need to do. Thank you for your continued interest.',
                ],
                'button' => null,
            ],
            'fail_probation' => [
                'name' => 'Did not pass probation', 'color' => $SLATE,
                'subject' => 'Probation result — {{ job_title }}', 'title' => 'Probation Result',
                'body' => [
                    'Hi <strong>{{ candidate_name }}</strong>,',
                    'Thank you for your efforts during your probation period as <strong>{{ job_title }}</strong> at <strong>{{ company }}</strong>.',
                    'After careful review, we have concluded that the role is not the right fit at this time, and your probation will not be confirmed. We wish you the very best going forward.',
                ],
                'button' => null,
            ],
        ];
    }
}
