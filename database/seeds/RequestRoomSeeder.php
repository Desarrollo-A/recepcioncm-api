<?php

use Illuminate\Database\Seeder;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;
use App\Models\Request;
use App\Models\RequestRoom;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;

class RequestRoomSeeder extends Seeder
{
    public $START_TIME_MORNING = '08:00:00.000';
    public $END_TIME_MORNING = '09:00:00.000';

    public $START_TIME_MIDDAY = '12:00:00.000';
    public $END_TIME_MIDDAY = '13:00:00.000';

    public $START_TIME_AFTERNOON = '16:00:00.000';
    public $END_TIME_AFTERNOON = '17:00:00.000';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statusNew = Lookup::query()
            ->where('type', TypeLookup::STATUS_ROOM_REQUEST)
            ->where('code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW))
            ->first()
            ->id;

        $typeRoom = Lookup::query()
            ->where('type', TypeLookup::TYPE_REQUEST)
            ->where('code', TypeRequestLookup::code(TypeRequestLookup::ROOM))
            ->first()
            ->id;

        $levelMeetings = Lookup::query()
            ->where('type', TypeLookup::LEVEL_MEETING)
            ->get();

        $roleApplicant = Role::query()->where('name', NameRole::APPLICANT)->first()->id;

        User::query()
            ->where('role_id', $roleApplicant)
            ->get()
            ->each(function ($user) use ($statusNew, $levelMeetings, $typeRoom) {
                Room::all()->each(function ($room) use ($user, $statusNew, $levelMeetings, $typeRoom) {
                    $date = now()->addDays(7)->toDateString();

                    $request = factory(Request::class)
                        ->create([
                            'start_date' => "$date $this->START_TIME_MORNING",
                            'end_date' => "$date $this->END_TIME_MORNING",
                            'user_id' => $user->id,
                            'status_id' => $statusNew,
                            'type_id' => $typeRoom
                        ]);

                    factory(RequestRoom::class)
                        ->create([
                            'request_id' => $request->id,
                            'room_id' => $room->id,
                            'level_id' => $levelMeetings->random()->id,
                            'duration' => 60
                        ]);

                    $request = factory(Request::class)
                        ->create([
                            'start_date' => "$date $this->START_TIME_MIDDAY",
                            'end_date' => "$date $this->END_TIME_MIDDAY",
                            'user_id' => $user->id,
                            'status_id' => $statusNew,
                            'type_id' => $typeRoom
                        ]);

                    factory(RequestRoom::class)
                        ->create([
                            'request_id' => $request->id,
                            'room_id' => $room->id,
                            'level_id' => $levelMeetings->random()->id,
                            'duration' => 60
                        ]);

                    $request = factory(Request::class)
                        ->create([
                            'start_date' => "$date $this->START_TIME_AFTERNOON",
                            'end_date' => "$date $this->END_TIME_AFTERNOON",
                            'user_id' => $user->id,
                            'status_id' => $statusNew,
                            'type_id' => $typeRoom
                        ]);

                    factory(RequestRoom::class)
                        ->create([
                            'request_id' => $request->id,
                            'room_id' => $room->id,
                            'level_id' => $levelMeetings->random()->id,
                            'duration' => 60
                        ]);
                });
            });
    }
}
