<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Switch existing workspaces to inheritance: delete workspace transactional
 * templates that are untouched clones of a default (name+subject+content+data_json
 * all identical). Customized overrides and custom codes are kept.
 */
class PruneUntouchedTransactionalClones extends Migration
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
                    $d = $defaults->get($t->code);
                    if (!$d) {
                        continue; // custom code → keep
                    }
                    $untouched =
                        (string) $t->name      === (string) $d->name &&
                        (string) $t->subject   === (string) $d->subject &&
                        (string) $t->content   === (string) $d->content &&
                        (string) $t->data_json === (string) $d->data_json;

                    if ($untouched) {
                        DB::table('sendportal_templates')->where('id', $t->id)->delete();
                    }
                }
            });
    }

    public function down(): void
    {
        // No-op: pruned clones are recoverable by inheriting the live default.
    }
}
