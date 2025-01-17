<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sendportal\Base\Models\UnsubscribeEventType;
use Sendportal\Base\UpgradeMigration;

class CreateUnsubscribedTable extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sendportal_unsubscribe_event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $types = [
            UnsubscribeEventType::BOUNCE => 'Bounce',
            UnsubscribeEventType::COMPLAINT => 'Complaint',
            UnsubscribeEventType::MANUAL_BY_ADMIN => 'Manual by Admin',
            UnsubscribeEventType::MANUAL_BY_SUBSCRIBER => 'Manual by Subscriber',
        ];

        foreach ($types as $id => $name) {
            DB::table('sendportal_unsubscribe_event_types')->insert([
                'id' => $id,
                'name' => $name
            ]);
        }
    }
}
