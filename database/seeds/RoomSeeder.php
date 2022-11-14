<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Role;
use App\Models\User;
use App\Models\Room;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\Lookups\StatusRoomLookup;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = Lookup::query()
            ->where('type', TypeLookup::STATUS_ROOM)
            ->where('code', StatusRoomLookup::code(StatusRoomLookup::ACTIVE))
            ->first()
            ->id;

        $roleRecepcionist = Role::query()->where('name', NameRole::RECEPCIONIST)->first()->id;

        User::query()
            ->where('role_id', $roleRecepcionist)
            ->get()
            ->each(function ($user) use ($status) {
                /*Room::query()->create([
                    'name' => 'Carranza',
                    'no_people' => 12,
                    'office_id' => $user->office_id,
                    'recepcionist_id' => $user->id,
                    'status_id' => $status
                ]);*/
                factory(Room::class, 2)
                    ->create([
                        'office_id' => $user->office_id,
                        'recepcionist_id' => $user->id,
                        'status_id' => $status
                    ]);
            });
    }
}
