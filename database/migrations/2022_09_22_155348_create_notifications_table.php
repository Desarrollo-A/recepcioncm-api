<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('message');
            $table->boolean('is_read')
                ->default(false);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->unsignedSmallInteger('type_id');
            $table->foreign('type_id')
                ->references('id')
                ->on('lookups');
            $table->unsignedSmallInteger('color_id');
            $table->foreign('color_id')
                ->references('id')
                ->on('lookups');
            $table->unsignedSmallInteger('icon_id');
            $table->foreign('icon_id')
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
        Schema::dropIfExists('notifications');
    }
}
