<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 75)
                ->nullable();
            $table->string('name', 120);
            $table->unsignedInteger('office_id');
            $table->foreign('office_id')->references('id')->on('offices');
            $table->smallInteger('no_people');
            $table->unsignedBigInteger('recepcionist_id');
            $table->foreign('recepcionist_id')
                ->references('id')
                ->on('users');
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
        Schema::dropIfExists('rooms');
    }
}
