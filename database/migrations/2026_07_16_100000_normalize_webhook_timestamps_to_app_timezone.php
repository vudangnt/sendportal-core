<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Unify message tracking timestamps to the app timezone (Asia/Ho_Chi_Minh, +07).
 *
 * History: sent_at / queued_at were written via now() in app timezone (+07),
 * while all webhook-driven columns (delivered_at, opened_at, clicked_at,
 * bounced_at, complained_at, messages.unsubscribed_at via the complaint
 * handler, and message_failures.failed_at) were written from provider events
 * normalized to UTC. A per-month fingerprint of AVG(delivered_at - sent_at)
 * confirmed a constant -7h gap across the entire dataset (2025-03 → 2026-07),
 * so a uniform +7h shift is correct for ALL existing rows.
 *
 * Going forward, EmailWebhookService::localize() stores webhook timestamps in
 * app timezone, so this shift is one-time. Deploy procedure must pause the
 * queue workers (horizon:pause) before migrating so no UTC-writing worker
 * runs after the shift.
 *
 * down() reverses the shift.
 */
class NormalizeWebhookTimestampsToAppTimezone extends Migration
{
    private const MESSAGE_COLUMNS = [
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'complained_at',
        'unsubscribed_at',
    ];

    public function up(): void
    {
        $offset = $this->offsetHours();

        foreach (self::MESSAGE_COLUMNS as $col) {
            DB::statement(
                "UPDATE sendportal_messages SET {$col} = DATE_ADD({$col}, INTERVAL {$offset} HOUR) WHERE {$col} IS NOT NULL"
            );
        }

        DB::statement(
            "UPDATE sendportal_message_failures SET failed_at = DATE_ADD(failed_at, INTERVAL {$offset} HOUR) WHERE failed_at IS NOT NULL"
        );
    }

    public function down(): void
    {
        $offset = $this->offsetHours();

        foreach (self::MESSAGE_COLUMNS as $col) {
            DB::statement(
                "UPDATE sendportal_messages SET {$col} = DATE_SUB({$col}, INTERVAL {$offset} HOUR) WHERE {$col} IS NOT NULL"
            );
        }

        DB::statement(
            "UPDATE sendportal_message_failures SET failed_at = DATE_SUB(failed_at, INTERVAL {$offset} HOUR) WHERE failed_at IS NOT NULL"
        );
    }

    /** Hours between UTC and the app timezone right now (7 for Asia/Ho_Chi_Minh). */
    private function offsetHours(): int
    {
        $tz = new DateTimeZone(config('app.timezone', 'UTC'));

        return intdiv($tz->getOffset(new DateTimeImmutable('now', new DateTimeZone('UTC'))), 3600);
    }
}
