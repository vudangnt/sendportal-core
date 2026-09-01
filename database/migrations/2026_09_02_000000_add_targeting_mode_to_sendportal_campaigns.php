<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ngữ nghĩa nhắm mục tiêu của từng campaign.
 *
 * 'legacy'  = (⋃ tag) AND (giao location) — y nguyên hành vi hôm nay
 * 'segment' = OR trong dimension, AND giữa dimension (tài liệu §8)
 *
 * Mặc định 'legacy' để 485 campaign hiện có không đổi một người nhận nào.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sendportal_campaigns', function (Blueprint $table) {
            $table->string('targeting_mode', 16)->default('legacy')->after('send_to_all');
        });
    }

    public function down(): void
    {
        Schema::table('sendportal_campaigns', function (Blueprint $table) {
            $table->dropColumn('targeting_mode');
        });
    }
};
