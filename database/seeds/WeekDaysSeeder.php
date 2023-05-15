<?php

use Illuminate\Database\Seeder;
use App\Models\Enums\Lookups\WeekDaysLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;

class WeekDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WeekDaysLookup::getAll()->each(function (string $lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::WEEK_DAYS,
                'code' => WeekDaysLookup::code($lookup),
                'value' => $lookup
            ]);
        });
    }
}
