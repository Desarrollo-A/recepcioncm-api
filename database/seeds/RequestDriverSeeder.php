<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Role;
use App\Models\User;
use App\Models\Office;
use App\Models\Enums\NameRole;
use App\Models\Address;
use App\Models\Request;
use App\Models\RequestDriver;

class RequestDriverSeeder extends Seeder
{
    const START_TIME_DAY = '09:00:00.000';
    const END_TIME_DAY = '14:00:00.000';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statusNew = Lookup::query()
            ->where('type', TypeLookup::STATUS_DRIVER_REQUEST)
            ->where('code', StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW))
            ->first()
            ->id;

        $typeDriver = Lookup::query()
            ->where('type', TypeLookup::TYPE_REQUEST)
            ->where('code', TypeRequestLookup::code(TypeRequestLookup::DRIVER))
            ->first()
            ->id;

        $roleApplicant = Role::query()->where('name', NameRole::APPLICANT)->first()->id;
        $roleDriver = Role::query()->where('name', NameRole::DRIVER)->first()->id;

        $countries = Lookup::query()
            ->where('type', TypeLookup::COUNTRY_ADDRESS)
            ->get('id');

        $date = now()->addDays(7)->toDateString();

        User::query()
            ->where('role_id', $roleApplicant)
            ->get('id')
            ->each(function ($user) use ($statusNew, $typeDriver, $countries, $date, $roleDriver) {
                $officesIds = Office::query()
                    ->whereIn('id', function(\Illuminate\Database\Query\Builder $query) use ($roleDriver) {
                        return $query->selectRaw('DISTINCT(office_id)')
                            ->from('users')
                            ->where('role_id', $roleDriver);
                    })
                    ->whereIn('id', function($query) {
                        return $query->selectRaw('DISTINCT(office_id)')
                            ->from('cars')
                            ->where('people', '>=', 2);
                    })
                    ->orderBy('name', 'ASC')
                    ->get('id');

                foreach (range(0,2) as $ignored) {
                    $pickupAddress = factory(Address::class)
                        ->create([
                            'country_id' => $countries->shuffle()->first()->id
                        ]);

                    $arrivalAddress = factory(Address::class)
                        ->create([
                            'country_id' => $countries->shuffle()->first()->id
                        ]);

                    $request = factory(Request::class)
                        ->create([
                            'start_date' => "$date ".self::START_TIME_DAY,
                            'end_date' => "$date ".self::END_TIME_DAY,
                            'user_id' => $user->id,
                            'status_id' => $statusNew,
                            'type_id' => $typeDriver,
                            'people' => 2
                        ]);

                    RequestDriver::query()
                        ->create([
                            'pickup_address_id' => $pickupAddress->id,
                            'arrival_address_id' => $arrivalAddress->id,
                            'request_id' => $request->id,
                            'office_id' => $officesIds->shuffle()->first()->id
                        ]);
                }
            });
    }
}
