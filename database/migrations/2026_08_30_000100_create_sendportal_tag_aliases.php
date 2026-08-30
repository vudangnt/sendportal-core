<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ánh xạ tên tự do → mã, cho những trường hợp normalizer không tự suy ra đúng
 * ("TP.HCM", "Sài Gòn", "HCMC" đều là LOC_HCM). Người vận hành thêm alias thay vì
 * sửa code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sendportal_tag_aliases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id');
            $table->string('dimension', 32);
            $table->string('alias', 191);          // đã lowercase + trim khi ghi
            $table->string('code', 64);
            $table->timestamps();

            $table->unique(['workspace_id', 'dimension', 'alias'], 'sendportal_tag_aliases_unique');
            $table->index(['workspace_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sendportal_tag_aliases');
    }
};
