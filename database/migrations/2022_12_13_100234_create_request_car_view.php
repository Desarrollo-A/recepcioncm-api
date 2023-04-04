<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestCarView extends Migration
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
        return 'CREATE OR ALTER VIEW request_car_view AS
            SELECT REQ.id AS request_id, REQ.code, REQ.title, REQ.start_date, REQ.end_date, 
                LPS.value AS status_name, LPS.code AS status_code, RQCAR.office_id, USU.full_name,
                REQ.user_id, RQCAR.id AS request_car_id
            FROM requests AS REQ
            INNER JOIN lookups AS LPS ON REQ.status_id = LPS.id
            INNER JOIN users AS USU ON REQ.user_id = USU.id
            INNER JOIN request_cars AS RQCAR ON REQ.id = RQCAR.request_id';
    }

    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS request_car_view';
    }
}
