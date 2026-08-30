<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nền tảng Data Tag (tài liệu Lark mục 3): một vốn từ vựng có prefix thay vì 5 taxonomy
 * song song. UNIQUE(workspace_id, code) là thứ chặn tag trùng/tag rác — mục tiêu
 * "Chuẩn hoá dữ liệu" của tài liệu.
 *
 * code NULLABLE ở bước này: dữ liệu cũ chưa có mã, backfill chạy sau (Task 1.6).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sendportal_tags', function (Blueprint $table) {
            $table->string('code', 64)->nullable()->after('name');
            $table->string('dimension', 32)->nullable()->after('code');
            $table->string('source', 32)->default('manual')->after('dimension');

            $table->unique(['workspace_id', 'code'], 'sendportal_tags_workspace_code_unique');
            $table->index('dimension', 'sendportal_tags_dimension_index');
        });
    }

    public function down(): void
    {
        Schema::table('sendportal_tags', function (Blueprint $table) {
            $table->dropUnique('sendportal_tags_workspace_code_unique');
            $table->dropIndex('sendportal_tags_dimension_index');
            $table->dropColumn(['code', 'dimension', 'source']);
        });
    }
};
