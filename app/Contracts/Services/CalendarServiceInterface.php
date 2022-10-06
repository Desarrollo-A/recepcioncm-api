<?php

namespace App\Contracts\Services;

use App\Models\User;

interface CalendarServiceInterface
{
    public function getDataCalendar(User $user);
}