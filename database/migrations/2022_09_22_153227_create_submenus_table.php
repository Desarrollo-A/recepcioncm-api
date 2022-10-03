<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submenus', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('path_route');
            $table->string('label', 120);
            $table->tinyInteger('order');
            $table->unsignedSmallInteger('menu_id');
            $table->foreign('menu_id')
                ->references('id')
                ->on('menus');
            $table->boolean('status')
                ->default(true);
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
        Schema::dropIfExists('submenus');
    }
}
