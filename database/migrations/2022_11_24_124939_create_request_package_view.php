<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestPackageView extends Migration
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
        return 'CREATE OR ALTER VIEW request_package_view AS
            SELECT r.id AS request_id, r.code, r.title, r.start_date, r.end_date, s.name AS status_name, s.code AS status_code, 
            p.office_id, u.full_name, pick.state AS state_pickup, arrv.state AS state_arrival, r.user_id, p.id AS package_id,
            ds.driver_id, dpk.name_receive, dpk.signature
            FROM packages p
            INNER JOIN requests r ON r.id = p.request_id
            INNER JOIN lookups s ON r.status_id = s.id
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN addresses pick ON pick.id = p.pickup_address_id
            INNER JOIN addresses arrv ON arrv.id = p.arrival_address_id
            LEFT JOIN delivered_packages dpk ON p.id = dpk.package_id
            LEFT JOIN driver_package_schedules dps ON p.id = dps.package_id
            LEFT JOIN driver_schedules ds ON ds.id = dps.driver_schedule_id';
    }

    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS request_package_view';
    }
}
