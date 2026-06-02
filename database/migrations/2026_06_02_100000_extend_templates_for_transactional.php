<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExtendTemplatesForTransactional extends Migration
{
    public function up(): void
    {
        Schema::table('sendportal_templates', function (Blueprint $table) {
            $table->string('code', 64)->nullable()->after('name');
            $table->string('subject', 998)->nullable()->after('code');
            $table->string('kind', 32)->default('campaign')->after('content');
            $table->boolean('is_default')->default(false)->after('kind');
        });

        DB::statement('ALTER TABLE sendportal_templates MODIFY workspace_id BIGINT UNSIGNED NULL');

        Schema::table('sendportal_templates', function (Blueprint $table) {
            $table->unique(['workspace_id', 'code'], 'idx_templates_ws_code');
            $table->index('code', 'idx_templates_code');
            $table->index('kind', 'idx_templates_kind');
            $table->index(['kind', 'code'], 'idx_templates_kind_code');
        });
    }

    public function down(): void
    {
        Schema::table('sendportal_templates', function (Blueprint $table) {
            $table->dropUnique('idx_templates_ws_code');
            $table->dropIndex('idx_templates_code');
            $table->dropIndex('idx_templates_kind');
            $table->dropIndex('idx_templates_kind_code');
            $table->dropColumn(['code', 'subject', 'kind', 'is_default']);
        });
        DB::statement('ALTER TABLE sendportal_templates MODIFY workspace_id BIGINT UNSIGNED NOT NULL');
    }
}
