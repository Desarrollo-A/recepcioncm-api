<?php

namespace App\Services;

use App\Contracts\Repositories\RequestRoomRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

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

    /**
     * @param array<string> $attendees
     * @return void
     */
    public function createEvent(string $title, Carbon $startDateTime, Carbon $endDateTime, array $attendees): Event
    {
        $event = new Event();
        $event->name = $title;
        $event->startDateTime = $startDateTime;
        $event->endDateTime = $endDateTime;
        foreach ($attendees as $email) {
            $event->addAttendee(['email' => $email]);
        }
        return $event->save();
    }

    /**
     * @return void
     */
    public function deleteEvent(string $eventId)
    {
        $event = Event::find($eventId);
        $event->delete();
    }
}