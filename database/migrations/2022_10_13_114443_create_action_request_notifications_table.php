<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionRequestNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_request_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('request_notification_id');
            $table->foreign('request_notification_id')
                ->references('id')
                ->on('request_notifications');
            $table->boolean('is_answered')->default(false);
            $table->unsignedInteger('type_id');
            $table->foreign('type_id')
                ->references('id')
                ->on('lookups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_request_notifications');
    }
}
