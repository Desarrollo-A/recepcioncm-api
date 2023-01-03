<?php

namespace App\Contracts\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Spatie\GoogleCalendar\Event;

interface CalendarServiceInterface
{
    public function getDataCalendar(User $user);

    /**
     * @param User|Authenticatable $user
     */
    public function getSummaryOfDay(User $user): Collection;

    /**
     * @param array<string> $attendees
     * @return void
     */
    public function createEvent(string $title, Carbon $startDateTime, Carbon $endDateTime, array $attendees): Event;

    /**
     * @return void
     */
    public function deleteEvent(string $eventId);
}