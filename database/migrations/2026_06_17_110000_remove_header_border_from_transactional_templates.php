<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Remove the 4px colored top border from the redesigned transactional email
 * shell. The per-status color still shows via the badge, the CTA button and
 * the info-box accent — just not as a bar across the top of the card.
 *
 * Strips the literal `border-top:4px solid #xxxxxx;` (color-agnostic) from any
 * transactional template content that has it (the 28 global defaults + the
 * workspace copies refreshed to the new shell). The footer/header 1px dividers
 * (`border-top:1px solid ...`) are NOT matched. Idempotent; down() is a no-op.
 */
class RemoveHeaderBorderFromTransactionalTemplates extends Migration
{
    public function up(): void
    {
        $now = now();

        $rows = DB::table('sendportal_templates')
            ->where('kind', 'transactional')
            ->where('content', 'like', '%border-top:4px solid%')
            ->get(['id', 'content']);

        foreach ($rows as $row) {
            $new = preg_replace('/border-top:4px solid #[0-9a-fA-F]{6};/', '', (string) $row->content);
            if ($new !== null && $new !== $row->content) {
                DB::table('sendportal_templates')
                    ->where('id', $row->id)
                    ->update(['content' => $new, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        // No-op: the colored top border is not restored automatically.
    }
}
