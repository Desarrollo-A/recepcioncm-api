<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Role;
use App\Models\User;
use App\Models\Office;
use App\Models\Enums\NameRole;
use App\Models\Request;
use App\Models\RequestCar;

class RequestCarSeeder extends Seeder
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
            ->where('type', TypeLookup::STATUS_CAR_REQUEST)
            ->where('code', StatusCarRequestLookup::code(StatusCarRequestLookup::NEW))
            ->first()
            ->id;

        $typeCar = Lookup::query()
            ->where('type', TypeLookup::TYPE_REQUEST)
            ->where('code', TypeRequestLookup::code(TypeRequestLookup::CAR))
            ->first()
            ->id;

        $roleApplicant = Role::query()->where('name', NameRole::APPLICANT)->first()->id;

        $date = now()->addDays(7)->toDateString();
        
        User::query()
            ->where('role_id', $roleApplicant)
            ->get('id')
            ->each(function ($user) use ($statusNew, $typeCar, $date) {
                $officesIds = Office::query()
                    ->whereIn('id', function($query) {
                        return $query->selectRaw('DISTINCT(office_id)')
                            ->from('cars')
                            ->where('people', '>=', 2);
                    })
                    ->orderBy('name', 'ASC')
                    ->get('id');

                foreach (range(0,2) as $ignored) {
                    $request = factory(Request::class)
                        ->create([
                            'start_date' => "$date ".self::START_TIME_DAY,
                            'end_date' => "$date ".self::END_TIME_DAY,
                            'user_id' => $user->id,
                            'status_id' => $statusNew,
                            'type_id' => $typeCar,
                            'people' => 2
                        ]);

                    RequestCar::query()
                        ->create([
                            'request_id' => $request->id,
                            'office_id' => $officesIds->shuffle()->first()->id
                        ]);
                }
            });
    }
}
