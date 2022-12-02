<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverRequestScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_request_schedule', function (Blueprint $table) {
            $table->unsignedBigInteger('request_driver_id');
            $table->foreign('request_driver_id')
                ->references('id')
                ->on('request_driver');
            $table->unsignedBigInteger('driver_schedule_id');
            $table->foreign('driver_schedule_id')
                ->references('id')
                ->on('driver_schedule');
            $table->unsignedBigInteger('car_schedule_id');
            $table->foreign('car_schedule_id')
                ->references('id')
                ->on('car_schedule');
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
        Schema::dropIfExists('driver_request_schedule');
    }
}
