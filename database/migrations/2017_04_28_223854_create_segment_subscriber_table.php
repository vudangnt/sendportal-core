<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sendportal\Base\UpgradeMigration;

class CreateSegmentSubscriberTable extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $segments = $this->getTableName('segments');
        $subscribers = $this->getTableName('subscribers');

        Schema::create('sendportal_segment_subscriber', function (Blueprint $table) use ($segments, $subscribers) {
            $table->id();
            $table->unsignedBigInteger('segment_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamps();

            $table->foreign('segment_id')->references('id')->on($segments);
            $table->foreign('subscriber_id')->references('id')->on($subscribers);
        });
    }
}
