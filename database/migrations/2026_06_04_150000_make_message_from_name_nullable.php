<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Transactional sends may have no display name (from.name is optional). The
 * messages.from_name column was NOT NULL without a default, so creating a
 * transactional/test message that omits from_name (or sets it null) failed with
 * SQLSTATE 1364 / 1048. Make it nullable.
 *
 * Raw SQL is used to avoid requiring doctrine/dbal for ->change() on Laravel 8.
 */
class MakeMessageFromNameNullable extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE sendportal_messages MODIFY from_name VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE sendportal_messages MODIFY from_name VARCHAR(255) NOT NULL');
    }
}
