<?php

namespace App\Contracts\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Spatie\GoogleCalendar\Event;

interface CalendarServiceInterface
{
    /**
     * @param User|Authenticatable $user
     */
    public function getDataCalendar(User $user): Collection;

    /**
     * @param User|Authenticatable $user
     */
    public function getSummaryOfDay(User $user): Collection;

    /**
     * @param array<string> $attendees
     * @return void
     */
    public function createEvent(string $title, Carbon $startDateTime, Carbon $endDateTime, array $attendees): Event;

    public function createEventAllDay(string $title, Carbon $date, array $attendees): Event;

    /**
     * @return void
     */
    public function deleteEvent(string $eventId);
}