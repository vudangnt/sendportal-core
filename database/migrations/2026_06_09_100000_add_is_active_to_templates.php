<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Super-admin enable/disable for transactional default templates. When a default
 * is disabled (is_active = false) it is hidden from the workspace transactional
 * tab (not shown as "inherited"); the send API still resolves it. Additive,
 * default true so existing templates are unaffected.
 */
class AddIsActiveToTemplates extends Migration
{
    public function up(): void
    {
        Schema::table('sendportal_templates', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_default');
        });
    }

    public function down(): void
    {
        Schema::table('sendportal_templates', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
