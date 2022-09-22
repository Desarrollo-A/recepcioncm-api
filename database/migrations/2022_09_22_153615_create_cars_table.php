<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('business_name');
            $table->string('trademark', 100);
            $table->string('model', 50);
            $table->string('color', 50);
            $table->string('license_plate', 10);
            $table->string('serie', 20);
            $table->string('circulation_card', 10);
            $table->smallInteger('people');
            $table->unsignedInteger('office_id');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
            $table->unsignedSmallInteger('status_id');
            $table->foreign('status_id')
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
        Schema::dropIfExists('cars');
    }
}
