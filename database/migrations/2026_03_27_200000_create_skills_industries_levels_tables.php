<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillsIndustriesLevelsTables extends Migration
{
    public function up()
    {
        // ==================== SKILLS ====================
        Schema::create('sendportal_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id');
            $table->string('name');
            $table->integer('parent_id')->default(0);
            $table->timestamps();

            $table->index('workspace_id');
        });

        Schema::create('sendportal_skill_subscriber', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('skill_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamps();

            $table->index('skill_id');
            $table->index('subscriber_id');
        });

        Schema::create('sendportal_campaign_skill', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('skill_id');
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('skill_id');
        });

        // ==================== INDUSTRIES ====================
        Schema::create('sendportal_industries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id');
            $table->string('name');
            $table->integer('parent_id')->default(0);
            $table->timestamps();

            $table->index('workspace_id');
        });

        Schema::create('sendportal_industry_subscriber', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('industry_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamps();

            $table->index('industry_id');
            $table->index('subscriber_id');
        });

        Schema::create('sendportal_campaign_industry', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('industry_id');
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('industry_id');
        });

        // ==================== LEVELS ====================
        Schema::create('sendportal_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id');
            $table->string('name');
            $table->integer('parent_id')->default(0);
            $table->timestamps();

            $table->index('workspace_id');
        });

        Schema::create('sendportal_level_subscriber', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('level_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamps();

            $table->index('level_id');
            $table->index('subscriber_id');
        });

        Schema::create('sendportal_campaign_level', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('level_id');
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('level_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sendportal_campaign_level');
        Schema::dropIfExists('sendportal_level_subscriber');
        Schema::dropIfExists('sendportal_levels');
        Schema::dropIfExists('sendportal_campaign_industry');
        Schema::dropIfExists('sendportal_industry_subscriber');
        Schema::dropIfExists('sendportal_industries');
        Schema::dropIfExists('sendportal_campaign_skill');
        Schema::dropIfExists('sendportal_skill_subscriber');
        Schema::dropIfExists('sendportal_skills');
    }
}
