<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 75)
                ->nullable();
            $table->index('code');
            $table->string('title', 100);
            $table->index('title');
            $table->datetime('start_date');
            $table->index('start_date');
            $table->datetime('end_date')
                ->nullable();
            $table->index('end_date');
            $table->unsignedInteger('type_id');
            $table->foreign('type_id')
                ->references('id')
                ->on('lookups');
            $table->text('comment')
                ->nullable();
            $table->boolean('add_google_calendar');
            $table->string('event_google_calendar_id', 50)
                ->nullable();
            $table->tinyInteger('people')
                ->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->unsignedInteger('status_id');
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
        Schema::dropIfExists('requests');
    }
}
