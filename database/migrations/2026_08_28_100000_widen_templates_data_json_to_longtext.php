<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Widen sendportal_templates.data_json from TEXT (64KB max) to LONGTEXT.
 *
 * Rich multi-block Unlayer designs (header/title/body/button/footer with
 * inline HTML + branding) exceed the 64KB TEXT limit, so saving a template
 * failed with: SQLSTATE[22001] Data too long for column 'data_json'.
 * `content` is already longtext; this brings data_json in line.
 */
class WidenTemplatesDataJsonToLongtext extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('sendportal_templates', 'data_json')) {
            DB::statement('ALTER TABLE sendportal_templates MODIFY data_json LONGTEXT NULL');
        }
    }

    public function down(): void
    {
        // No-op: narrowing back to TEXT could truncate existing designs > 64KB.
    }
}
