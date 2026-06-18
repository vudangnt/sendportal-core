<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Services\Templates\UnlayerDesignBuilder;

/**
 * Backfill `data_json` for transactional templates that lost it during the
 * 2026-06-17 redesign (which set data_json = NULL because the new shell is
 * hand-crafted raw HTML). The workspace visual editor loads its canvas from
 * data_json, so a NULL value opens the editor blank. Here we regenerate a
 * matching Unlayer design (the content wrapped in one HTML block) so the
 * editor renders the exact email and editing/cloning works again.
 *
 * Only touches rows with NULL data_json + non-empty content (the 28 redesigned
 * global defaults + workspace copies refreshed to the new shell). Templates
 * that already have a design (untouched/customized copies, campaign templates)
 * are left alone. Idempotent; down() is a no-op.
 */
class BackfillUnlayerDesignForTransactionalTemplates extends Migration
{
    public function up(): void
    {
        $now = now();

        $rows = DB::table('sendportal_templates')
            ->where('kind', 'transactional')
            ->whereNull('data_json')
            ->whereNotNull('content')
            ->where('content', '<>', '')
            ->get(['id', 'content']);

        foreach ($rows as $row) {
            DB::table('sendportal_templates')
                ->where('id', $row->id)
                ->update([
                    'data_json'  => UnlayerDesignBuilder::fromHtml((string) $row->content),
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // No-op: these rows had NULL data_json before; not restored.
    }
}
