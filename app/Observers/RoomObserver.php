<?php

namespace App\Observers;

use App\Contracts\Services\RoomServiceInterface;
use App\Models\Room;

class RoomObserver
{
    private $roomService;

    public function __construct(RoomServiceInterface $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * Handle the Room "created" event.
     *
     * @param Room $room
     * @return void
     */
    public function created(Room $room)
    {
        $this->roomService->updateCode($room);
    }
}