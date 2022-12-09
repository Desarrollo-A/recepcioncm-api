<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pickup_address_id');
            $table->foreign('pickup_address_id')
                ->references('id')
                ->on('addresses');
            $table->unsignedBigInteger('arrival_address_id');
            $table->foreign('arrival_address_id')
                ->references('id')
                ->on('addresses');
            $table->string('authorization_filename', 50)
                ->nullable();
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')
                ->references('id')
                ->on('requests')
                ->onDelete('cascade');
            $table->unsignedInteger('office_id');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
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
        Schema::dropIfExists('request_drivers');
    }
}
