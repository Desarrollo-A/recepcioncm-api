<?php

namespace App\Services;

use App\Contracts\Repositories\RequestRoomRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Models\User;

class CalendarService implements CalendarServiceInterface
{
    private $requestRoomRepository;

    public function __construct(RequestRoomRepositoryInterface $requestRoomRepository)
    {
        $this->requestRoomRepository = $requestRoomRepository;
    }

    public function getDataCalendar(User $user)
    {
        return $this->requestRoomRepository->getDataCalendar($user);
    }

    public function getSummaryOfDay(User $user)
    {
        return $this->requestRoomRepository->getSummaryOfDay($user);
    }
}