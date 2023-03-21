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
            $table->string('no_employee', 50);
            $table->unique('no_employee');
            $table->string('full_name');
            $table->string('email', 150);
            $table->unique('email');
            $table->string('password');
            $table->string('personal_phone', 10);
            $table->string('office_phone', 10)
                ->nullable();
            $table->string('position', 100);
            $table->string('area', 100);
            $table->unsignedInteger('status_id');
            $table->foreign('status_id')
                ->references('id')
                ->on('lookups');
            $table->unsignedTinyInteger('role_id');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles');
            $table->unsignedInteger('office_id')
                ->nullable();
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
            $table->unsignedBigInteger('department_manager_id')
                ->nullable();
            $table->foreign('department_manager_id')
                ->references('id')
                ->on('users');
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
