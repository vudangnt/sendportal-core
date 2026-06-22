<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Services\Templates\TransactionalTemplateDesignBuilder;
use Sendportal\Base\Services\Templates\TransactionalTemplateSeedData;

/**
 * Replace the single opaque "html" Unlayer design (produced by the 2026_06_18
 * backfill, which wraps the whole email in one block) with a NATIVE MULTI-BLOCK
 * design for every default transactional template.
 *
 * Why: the previous design loaded fine in Unlayer but the entire email was a
 * single, non-editable HTML block — users could only edit raw HTML, not the
 * title / body / button via the visual editor. The multi-block design built by
 * {@see TransactionalTemplateDesignBuilder} lays the same email out as discrete
 * editable blocks (header / title / body / button / footer) while keeping the
 * branding composites in html blocks so an edit→Save round-trip preserves them.
 *
 * Scope (data_json only — `content`, used for sending, is left untouched):
 *   - global defaults (workspace_id NULL, kind transactional) by code
 *   - workspace copies whose `content` is byte-identical to the matching default
 *     (i.e. untouched/inherited copies tracking the default look); customized
 *     copies (different content) are left alone.
 *
 * Idempotent. down() is a no-op (pre-deploy DB backup is the safety net).
 */
class SeedMultiblockUnlayerDesignsForTransactionalTemplates extends Migration
{
    public function up(): void
    {
        $now  = now();
        $data = TransactionalTemplateSeedData::all();

        // Current global-default content per code (used to detect untouched
        // workspace copies; content is NOT modified by this migration).
        $defaultContents = DB::table('sendportal_templates')
            ->whereNull('workspace_id')->where('kind', 'transactional')
            ->whereIn('code', array_keys($data))
            ->pluck('content', 'code');

        foreach ($data as $code => $tpl) {
            $design = TransactionalTemplateDesignBuilder::designJson($tpl);

            // 1) Global default
            DB::table('sendportal_templates')
                ->whereNull('workspace_id')->where('kind', 'transactional')->where('code', $code)
                ->update(['data_json' => $design, 'updated_at' => $now]);

            // 2) Untouched workspace copies tracking the default look
            $defaultContent = $defaultContents[$code] ?? null;
            if ($defaultContent !== null) {
                DB::table('sendportal_templates')
                    ->whereNotNull('workspace_id')->where('kind', 'transactional')->where('code', $code)
                    ->where('content', $defaultContent)
                    ->update(['data_json' => $design, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        // No-op: previous (single-block) design is not restored from here.
        // Restore from the pre-deploy DB backup if needed.
    }
}
