<?php

namespace App\Contracts\Services;

use App\Models\User;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

interface CalendarServiceInterface
{
    public function getDataCalendar(User $user);

    public function getSummaryOfDay(User $user);

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