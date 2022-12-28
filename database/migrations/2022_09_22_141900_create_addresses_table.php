<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('street', 150);
            $table->string('num_ext', 50);
            $table->string('num_int', 50)
                ->nullable();
            $table->string('suburb', 120)
                ->nullable();
            $table->string('postal_code', 25)
                ->nullable();
            $table->string('state', 100);
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')
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
        Schema::dropIfExists('addresses');
    }
}
