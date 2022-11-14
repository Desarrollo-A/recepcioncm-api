<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Office;
use App\Models\Driver;
use App\Models\User;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\StatusDriverLookup;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = Lookup::query()
            ->where('type', TypeLookup::STATUS_DRIVER)
            ->where('code', StatusDriverLookup::code(StatusDriverLookup::ACTIVE))
            ->first()
            ->id;

        User::query()
            ->whereHas('role', function ($query) {
                return $query->where('name', \App\Models\Enums\NameRole::RECEPCIONIST);
            })
            ->get()->each(function (User $user) use ($status) {
                factory(Driver::class, 2)
                    ->create([
                        'office_id' => $user->office_id,
                        'status_id' => $status
                    ]);
            });
    }
}
