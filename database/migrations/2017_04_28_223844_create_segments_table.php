<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sendportal\Base\UpgradeMigration;

class CreateSegmentsTable extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sendportal_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workspace_id')->index();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }
}
