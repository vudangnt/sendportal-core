<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUrlColumnInMessageUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sendportal_message_urls', function (Blueprint $table) {
            $table->dropIndex(['url']);
        });

        Schema::table('sendportal_message_urls', function (Blueprint $table) {
            $table->text('url')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sendportal_message_urls', function (Blueprint $table) {
            $table->string('url')->change();
        });

        Schema::table('sendportal_message_urls', function (Blueprint $table) {
            $table->index('url');
        });
    }
}
