<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sendportal\Base\UpgradeMigration;

class CreateMessagesTable extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sendportal_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('hash')->unique();
            $table->unsignedBigInteger('workspace_id')->index();
            $table->unsignedBigInteger('subscriber_id')->index();
            $table->string('source_type')->index();
            $table->unsignedBigInteger('source_id')->index();
            $table->string('recipient_email');
            $table->string('subject');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('message_id')->index()->nullable();
            $table->string('ip')->nullable();
            $table->unsignedBigInteger('open_count')->default(0);
            $table->unsignedBigInteger('click_count')->default(0);
            $table->timestamp('queued_at')->nullable()->default(null)->index();
            $table->timestamp('sent_at')->nullable()->default(null)->index();
            $table->timestamp('delivered_at')->nullable()->default(null)->index();
            $table->timestamp('bounced_at')->nullable()->default(null)->index();
            $table->timestamp('unsubscribed_at')->nullable()->default(null)->index();
            $table->timestamp('complained_at')->nullable()->default(null)->index();
            $table->timestamp('opened_at')->nullable()->default(null)->index();
            $table->timestamp('clicked_at')->nullable()->default(null)->index();
            $table->timestamps();
        });
    }
}
