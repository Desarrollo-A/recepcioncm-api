<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
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
            $table->string('name_receive', 150);
            $table->string('email_receive', 150);
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')
                ->references('id')
                ->on('requests')
                ->onDelete('cascade');
            $table->unsignedInteger('office_id');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
            $table->string('tracking_code', 25)
                ->nullable()
                ->index();
            $table->string('url_tracking')
                ->nullable();
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
        Schema::dropIfExists('packages');
    }
}
