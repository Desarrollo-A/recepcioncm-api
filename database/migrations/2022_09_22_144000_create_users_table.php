<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_employee', 50)->unique();
            $table->string('email');
            $table->unique('email');
            $table->string('password');
            $table->string('personal_phone', 10);
            $table->string('office_phone', 10)
                ->nullable();
            $table->string('position', 100);
            $table->string('area', 100);
            $table->unsignedSmallInteger('status_id');
            $table->foreign('status_id')
                ->references('id')
                ->on('lookups')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('role_id');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->unsignedInteger('office_id');
            $table->foreign('office_id')
                ->nullable()
                ->references('id')
                ->on('offices')
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
        Schema::dropIfExists('users');
    }
}
