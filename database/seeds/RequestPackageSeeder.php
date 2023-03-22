<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Role;
use App\Models\User;
use App\Models\Office;
use App\Models\Enums\NameRole;
use App\Models\Address;
use App\Models\Request;
use App\Models\Package;

class RequestPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statusInReviewManager = Lookup::query()
            ->where('type', TypeLookup::STATUS_PACKAGE_REQUEST)
            ->where('code', StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW_MANAGER))
            ->first()
            ->id;

        $typePackage = Lookup::query()
            ->where('type', TypeLookup::TYPE_REQUEST)
            ->where('code', TypeRequestLookup::code(TypeRequestLookup::PARCEL))
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
            ->each(function ($user) use ($statusInReviewManager, $typePackage, $countries, $date, $roleDriver) {
                $officesIds = Office::query()
                    ->whereIn('id', function(\Illuminate\Database\Query\Builder $query) use ($roleDriver) {
                        return $query->selectRaw('DISTINCT(office_id)')
                            ->from('users')
                            ->where('role_id', $roleDriver);
                    })
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
                            'start_date' => "$date 00:00:00.000",
                            'user_id' => $user->id,
                            'status_id' => $statusInReviewManager,
                            'type_id' => $typePackage,
                            'people' => null
                        ]);

                    factory(Package::class)
                        ->create([
                            'pickup_address_id' => $pickupAddress->id,
                            'arrival_address_id' => $arrivalAddress->id,
                            'request_id' => $request->id,
                            'office_id' => $officesIds->shuffle()->first()->id,
                            'is_urgent' => false
                        ]);
                }
            });
    }
}
