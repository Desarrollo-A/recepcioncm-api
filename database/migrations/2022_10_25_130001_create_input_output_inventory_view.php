<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInputOutputInventoryView extends Migration
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
        return 'CREATE OR ALTER VIEW input_output_inventory_view AS
            SELECT i.code, i.name, i.office_id, SUM(quantity) AS sum_quantity, SUM(cost) AS sum_cost,
            t.value AS type, t.id AS type_id, CAST(ih.created_at AS DATE) AS move_date
            FROM inventory_history ih
            JOIN inventories i ON ih.inventory_id = i.id
            JOIN lookups t ON t.id = i.type_id
            WHERE cost IS NOT NULL
            GROUP BY i.code, i.name, i.office_id, t.value, t.id, CAST(ih.created_at AS DATE)
                        
            UNION ALL
                        
            SELECT i.code, i.name, i.office_id, SUM(quantity) AS sum_quantity, NULL AS sum_cost,
            t.value AS type, t.id AS type_id, CAST(ih.created_at AS DATE) AS move_date
            FROM inventory_history ih
            JOIN inventories i ON ih.inventory_id = i.id
            JOIN lookups t ON t.id = i.type_id
            WHERE cost IS NULL
            GROUP BY i.code, i.name, i.office_id, t.value, t.id, CAST(ih.created_at AS DATE)';
    }

    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS input_output_inventory_view';
    }
}
