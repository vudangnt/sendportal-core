<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionalSourcesTable extends Migration
{
    public function up(): void
    {
        Schema::create('sendportal_transactional_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id');
            $table->uuid('hash')->unique();
            $table->json('request_payload')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'created_at']);

            $table->foreign('workspace_id')
                ->references('id')
                ->on('workspaces')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sendportal_transactional_sources');
    }
}
