<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedDefaultTransactionalTemplates extends Migration
{
    private array $rows = [
        [
            'code' => 'applied',
            'name' => 'Application received',
            'subject' => 'Application received: {{ job_title }}',
            'content' => '<p>Hi {{ candidate_name }},</p><p>We received your application for <strong>{{ job_title }}</strong> at {{ company }}. Our team will review it and get back to you shortly.</p><p>Thank you,<br>The {{ company }} team</p>',
        ],
        [
            'code' => 'shortlist',
            'name' => 'Shortlisted',
            'subject' => "You've been shortlisted for {{ job_title }}",
            'content' => '<p>Hi {{ candidate_name }},</p><p>Good news — you have been shortlisted for the <strong>{{ job_title }}</strong> role at {{ company }}. We will reach out shortly with next steps.</p>',
        ],
        [
            'code' => 'interviewed',
            'name' => 'Interview confirmation',
            'subject' => 'Interview scheduled: {{ job_title }}',
            'content' => '<p>Hi {{ candidate_name }},</p><p>Your interview for <strong>{{ job_title }}</strong> at {{ company }} has been scheduled. We look forward to speaking with you.</p>',
        ],
        [
            'code' => 'offered',
            'name' => 'Job offer',
            'subject' => 'Job offer — {{ company }}',
            'content' => '<p>Hi {{ candidate_name }},</p><p>Congratulations! We are pleased to extend an offer for the <strong>{{ job_title }}</strong> role at {{ company }}.</p>',
        ],
        [
            'code' => 'onboard_probation',
            'name' => 'Onboarding and probation',
            'subject' => 'Welcome aboard, {{ candidate_name }}',
            'content' => '<p>Hi {{ candidate_name }},</p><p>Welcome to {{ company }}. Onboarding details and your probation plan will follow shortly.</p>',
        ],
        [
            'code' => 'fail',
            'name' => 'Application result',
            'subject' => 'Application status — {{ job_title }}',
            'content' => '<p>Hi {{ candidate_name }},</p><p>Thank you for applying for <strong>{{ job_title }}</strong> at {{ company }}. After careful consideration we will not be moving forward at this time. We wish you the best in your search.</p>',
        ],
    ];

    public function up(): void
    {
        $now = now();
        foreach ($this->rows as $row) {
            DB::table('sendportal_templates')->updateOrInsert(
                ['workspace_id' => null, 'code' => $row['code']],
                array_merge($row, [
                    'workspace_id' => null,
                    'kind' => 'transactional',
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('sendportal_templates')
            ->whereNull('workspace_id')
            ->where('kind', 'transactional')
            ->where('is_default', true)
            ->whereIn('code', array_column($this->rows, 'code'))
            ->delete();
    }
}
