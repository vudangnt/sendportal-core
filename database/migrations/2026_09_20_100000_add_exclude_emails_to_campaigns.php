<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional per-campaign suppression list: a comma-separated set of email
 * addresses to skip when the campaign is sent (in addition to the normal
 * tag targeting). Null = suppress nothing.
 *
 * Rolled out to the transactional deployment only; the column is nullable and
 * the send path is null-safe, so deployments without this column (e.g. the
 * marketing site) are unaffected even though they share sendportal-core.
 */
class AddExcludeEmailsToCampaigns extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sendportal_campaigns', 'exclude_emails')) {
            Schema::table('sendportal_campaigns', function (Blueprint $table) {
                $table->text('exclude_emails')->nullable()->after('reply_to_email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sendportal_campaigns', 'exclude_emails')) {
            Schema::table('sendportal_campaigns', function (Blueprint $table) {
                $table->dropColumn('exclude_emails');
            });
        }
    }
}
