<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_id');
            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications')
                ->onDelete('cascade');
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')
                ->references('id')
                ->on('requests')
                ->onDelete('cascade');
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
        Schema::dropIfExists('request_notifications');
    }
}
