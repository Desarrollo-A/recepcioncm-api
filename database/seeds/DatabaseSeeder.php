<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->call(StateSeeder::class);
        $this->call(LookupSeeder::class);
        $this->call(OfficeSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserAdminSeeder::class);
        $this->call(UserManagerSeeder::class);
        // $this->call(UserSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(MenuUserSeeder::class);
        /*$this->call(RoomSeeder::class);
        $this->call(CarSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(InventoryHistorySeeder::class);
        $this->call(RequestRoomSeeder::class);
        $this->call(RequestPackageSeeder::class);
        $this->call(RequestDriverSeeder::class);
        $this->call(RequestCarSeeder::class);*/
        $this->call(WeekDaysSeeder::class);

        Schema::enableForeignKeyConstraints();
    }
}
