<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_employee', 50);
            $table->unique('no_employee');
            $table->string('full_name');
            $table->string('email', 150);
            $table->unique('email');
            $table->string('personal_phone', 10);
            $table->string('office_phone', 10)
                ->nullable();
            $table->unsignedInteger('office_id')
                ->nullable();
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
        Schema::dropIfExists('drivers');
    }
}
