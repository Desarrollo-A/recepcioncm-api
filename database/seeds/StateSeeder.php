<?php

use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::query()->create(['name' => 'QRO']);
        State::query()->create(['name' => 'LEON']);
        State::query()->create(['name' => 'SLP']);
        State::query()->create(['name' => 'CDMX']);
        State::query()->create(['name' => 'MERIDA']);
        State::query()->create(['name' => 'CELAYA']);
        State::query()->create(['name' => 'CANCUN']);
        State::query()->create(['name' => 'GDL']);
        State::query()->create(['name' => 'TIJUANA']);
        State::query()->create(['name' => 'SAN MIGUEL DE ALLENDE']);
    }
}
