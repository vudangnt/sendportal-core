<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Models\Template;

class BackfillTransactionalTemplatesToExistingWorkspaces extends Migration
{
    public function up(): void
    {
        $defaults = Template::transactional()
            ->whereNull('workspace_id')
            ->where('is_default', true)
            ->get();

        if ($defaults->isEmpty()) {
            return;
        }

        DB::table('workspaces')->orderBy('id')->chunkById(100, function ($workspaces) use ($defaults) {
            foreach ($workspaces as $ws) {
                $hasAny = Template::transactional()->where('workspace_id', $ws->id)->exists();
                if ($hasAny) {
                    continue;
                }
                foreach ($defaults as $d) {
                    Template::firstOrCreate(
                        ['workspace_id' => $ws->id, 'code' => $d->code],
                        [
                            'kind'       => Template::KIND_TRANSACTIONAL,
                            'name'       => $d->name,
                            'subject'    => $d->subject,
                            'content'    => $d->content,
                            'is_default' => false,
                        ]
                    );
                }
            }
        });
    }

    public function down(): void
    {
        // Removing copies would destroy customisations; intentionally a no-op.
    }
}
