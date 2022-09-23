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
        Schema::enableForeignKeyConstraints();

        $this->call(StateSeeder::class);
        $this->call(OfficeSeeder::class);
        $this->call(LookupSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserAdminSeeder::class);
        $this->call(UserSeeder::class);

        Schema::disableForeignKeyConstraints();
    }
}
