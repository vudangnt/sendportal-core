<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Old-clone cleanup: earlier the "Customize" clone path copied name/subject/content
 * but NOT data_json, leaving workspace templates with HTML content yet an empty
 * Unlayer design (editor opens blank). The earlier prune kept them because their
 * empty data_json differed from the default's.
 *
 * Delete those artifacts — a workspace transactional template with EMPTY data_json
 * whose name+subject+content match the default of the same code — so the workspace
 * inherits the live default (which carries both content and design). Templates that
 * carry their own data_json (real designs) or differ from the default are kept.
 */
class PruneBlankDatajsonTransactionalClones extends Migration
{
    public function up(): void
    {
        $defaults = DB::table('sendportal_templates')
            ->where('kind', 'transactional')
            ->whereNull('workspace_id')
            ->where('is_default', true)
            ->get()
            ->keyBy('code');

        DB::table('sendportal_templates')
            ->where('kind', 'transactional')
            ->whereNotNull('workspace_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($defaults) {
                foreach ($rows as $t) {
                    if (strlen((string) $t->data_json) > 0) {
                        continue; // has its own design → keep
                    }
                    $d = $defaults->get($t->code);
                    if (!$d) {
                        continue; // custom code → keep
                    }
                    $matchesDefault =
                        (string) $t->name    === (string) $d->name &&
                        (string) $t->subject === (string) $d->subject &&
                        (string) $t->content === (string) $d->content;

                    if ($matchesDefault) {
                        DB::table('sendportal_templates')->where('id', $t->id)->delete();
                    }
                }
            });
    }

    public function down(): void
    {
        // No-op: pruned blank clones are recoverable by inheriting the live default.
    }
}
