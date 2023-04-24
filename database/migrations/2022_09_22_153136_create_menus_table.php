<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path_route');
            $table->string('label', 120);
            $table->string('icon', 100);
            $table->tinyInteger('order');
            $table->boolean('status')
                ->default(true);
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles');
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
        Schema::dropIfExists('menus');
    }
}
