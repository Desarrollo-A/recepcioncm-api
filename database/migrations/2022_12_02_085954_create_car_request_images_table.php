<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarRequestImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_request_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image', 50);
            $table->unsignedBigInteger('request_car_id');
            $table->foreign('request_car_id')
                ->references('id')
                ->on('request_cars');
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
        Schema::dropIfExists('car_request_images');
    }
}
