<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenderDomainsToEmailServices extends Migration
{
    public function up(): void
    {
        Schema::table('sendportal_email_services', function (Blueprint $table) {
            $table->json('sender_domains')->nullable()->after('settings');
            $table->boolean('is_default')->default(false)->after('sender_domains');
        });
    }

    public function down(): void
    {
        Schema::table('sendportal_email_services', function (Blueprint $table) {
            $table->dropColumn(['sender_domains', 'is_default']);
        });
    }
}
