<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestRoomView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement($this->dropView());
    }

    public function createView(): string
    {
        return 'CREATE OR ALTER VIEW request_room_view AS
            SELECT r.id, r.title, r.code, r.start_date, r.end_date, u.full_name, ro.office_id, r.user_id, s.value AS status_name,
            ro.name AS room_name, l.value AS level_meeting, s.code AS status_code
            FROM request_room rr
            INNER JOIN requests r ON r.id = rr.request_id
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN lookups s ON s.id = r.status_id
            INNER JOIN lookups l ON l.id = rr.level_id
            INNER JOIN rooms ro ON ro.id = rr.room_id';
    }

    public function dropView(): string
    {
        return 'DROP VIEW IF EXISTS request_room_view';
    }
}
