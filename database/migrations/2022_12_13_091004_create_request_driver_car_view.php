<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestDriverCarView extends Migration
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

    private function createView(): string
    {
        return 'CREATE OR ALTER VIEW request_driver_view AS
            SELECT r.id AS request_id, r.code, r.title, r.start_date, r.end_date, s.name AS status_name, 
            s.code AS status_code, rd.office_id, u.full_name, pick.state AS state_pickup, 
            arrv.state AS state_arrival, r.user_id, rd.id AS request_driver_id
            FROM request_drivers rd
            INNER JOIN requests r ON r.id = rd.request_id
            INNER JOIN lookups s ON r.status_id = s.id
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN addresses pick ON pick.id = rd.pickup_address_id
            INNER JOIN addresses arrv ON arrv.id = rd.arrival_address_id';
    }

    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS request_driver_view';
    }
}
