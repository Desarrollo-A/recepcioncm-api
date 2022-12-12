<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 75)
                ->nullable();
            $table->index('code');
            $table->string('name', 50);
            $table->string('description')
                ->nullable();
            $table->string('trademark', 100)
                ->nullable();
            $table->smallInteger('stock');
            $table->tinyInteger('minimum_stock');
            $table->boolean('status')
                ->default(true);
            $table->string('image', 50);
            $table->unsignedInteger('type_id');
            $table->foreign('type_id')
                ->references('id')
                ->on('lookups');
            $table->unsignedInteger('unit_id');
            $table->foreign('unit_id')
                ->references('id')
                ->on('lookups');
            $table->unsignedInteger('office_id');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
            $table->tinyInteger('meeting')
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
        Schema::dropIfExists('inventories');
    }
}
