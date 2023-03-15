<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarRequestSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_request_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('request_car_id');
            $table->foreign('request_car_id')
                ->references('id')
                ->on('request_cars')
                ->onDelete('cascade');
            $table->unsignedBigInteger('car_schedule_id');
            $table->foreign('car_schedule_id')
                ->references('id')
                ->on('car_schedules')
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
        Schema::dropIfExists('car_request_schedules');
    }
}
