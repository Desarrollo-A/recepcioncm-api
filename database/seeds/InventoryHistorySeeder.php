<?php

use Illuminate\Database\Seeder;

class InventoryHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventories = \App\Models\Inventory::query()
            ->whereNull('meeting')
            ->get()
            ->each(function (\App\Models\Inventory $inventory) {
                for ($i = 0; $i < 5; $i++) {
                    $quantity = rand(1,10);
                    $cost = rand(100, 1000);
                    $createdAt = now()->subDays(rand(1,5));
                    \App\Models\InventoryHistory::query()->create([
                        'inventory_id' => $inventory->id,
                        'quantity' => $quantity,
                        'cost' => $cost,
                        'created_at' => $createdAt
                    ]);
                }
                for ($i = 0; $i < 10; $i++) {
                    $quantity = rand(-10,-1);
                    $createdAt = now()->subDays(rand(1,5));
                    \App\Models\InventoryHistory::query()->create([
                        'inventory_id' => $inventory->id,
                        'quantity' => $quantity,
                        'created_at' => $createdAt
                    ]);
                }
            });
    }
}
