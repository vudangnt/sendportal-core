<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an optional Reply-To email to campaigns (and denormalises it onto each
 * message, like from_email) so replies from subscribers go to an address the
 * campaign creator chooses. Null = no Reply-To header (provider default).
 */
class AddReplyToEmailToCampaignsAndMessages extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sendportal_campaigns', 'reply_to_email')) {
            Schema::table('sendportal_campaigns', function (Blueprint $table) {
                $table->string('reply_to_email')->nullable()->after('from_email');
            });
        }

        if (! Schema::hasColumn('sendportal_messages', 'reply_to_email')) {
            Schema::table('sendportal_messages', function (Blueprint $table) {
                $table->string('reply_to_email')->nullable()->after('from_email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sendportal_campaigns', 'reply_to_email')) {
            Schema::table('sendportal_campaigns', function (Blueprint $table) {
                $table->dropColumn('reply_to_email');
            });
        }

        if (Schema::hasColumn('sendportal_messages', 'reply_to_email')) {
            Schema::table('sendportal_messages', function (Blueprint $table) {
                $table->dropColumn('reply_to_email');
            });
        }
    }
}
