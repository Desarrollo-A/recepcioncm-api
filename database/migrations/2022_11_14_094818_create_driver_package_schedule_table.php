<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverPackageScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_package_schedule', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id');
            $table->foreign('package_id')
                ->references('id')
                ->on('packages');
            $table->unsignedBigInteger('driver_schedule_id');
            $table->foreign('driver_schedule_id')
                ->references('id')
                ->on('driver_schedule');
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
        Schema::dropIfExists('driver_package_schedule');
    }
}
