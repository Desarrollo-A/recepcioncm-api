<?php

namespace App\Services;

use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\RequestRoomRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Helpers\Utils;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\GoogleCalendar\Event;

class CalendarService implements CalendarServiceInterface
{
    private $requestRoomRepository;
    protected $requestRepository;

    public function __construct(RequestRoomRepositoryInterface $requestRoomRepository, 
                                RequestRepositoryInterface $requestRepository)
    {
        $this->requestRoomRepository = $requestRoomRepository;
        $this->requestRepository = $requestRepository;
    }

    public function getDataCalendar(User $user)
    {
        return $this->requestRoomRepository->getDataCalendar($user);
    }

    public function getSummaryOfDay(User $user): Collection
    {
        $roleName = $user->role->name;
        if ($roleName === NameRole::RECEPCIONIST) {
            return $this->requestRepository->getRecepcionistSummaryOfDay($user->office_id)
                ->map(function (Request $request) use ($user) {
                    $startDate = new Carbon($request->start_date);
                    $endDate = new Carbon($request->end_date);
                    $typeRequestCode = $request->type->code;

                    if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::ROOM)) {
                        $roomName = $request->requestRoom->room->name;
                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de sala",
                            "{$startDate->format('g:i A')} - {$endDate->format('g:i A')}, Sala $roomName",
                            $request
                        );
                    } else if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::PARCEL)) {
                        $driverName = $request->package->driverPackageSchedule->driverSchedule->driver->full_name;
                        $car = $request->package->driverPackageSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de paquetería",
                            "Chofer $driverName, Vehículo $car->trademark $car->model Placa $car->license_plate",
                            $request
                        );
                    } else if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::DRIVER)) {
                        $driverName = $request->requestDriver->driverRequestSchedule->driverSchedule->driver->full_name;
                        $car = $request->requestDriver->driverRequestSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de chofer",
                            "Chofer $driverName, Vehículo $car->trademark $car->model Placa $car->license_plate. ".
                            "Recoger a las {$startDate->format('g:i A')}",
                            $request
                        );
                    } else if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::CAR)) {
                        $car = $request->requestCar->carRequestSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de vehículo",
                            "Vehículo $car->trademark $car->model Placa $car->license_plate. ".
                            "Entregar a las {$startDate->format('g:i A')}",
                            $request
                        );
                    }

                    return Utils::createSummaryOfDayObject('','', new Request());
                });
        } else if ($roleName === NameRole::APPLICANT) {
            return $this->requestRepository->getApplicantSummaryOfDay($user->id)
                ->map(function (Request $request) use ($user) {
                    $startDate = new Carbon($request->start_date);
                    $endDate = new Carbon($request->end_date);
                    $typeRequestCode = $request->type->code;

                    if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::ROOM)) {
                        $room = $request->requestRoom->room;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de sala",
                            "{$startDate->format('g:i A')} - {$endDate->format('g:i A')}, ".
                            "Oficina {$room->office->name} en Sala $room->name",
                            $request
                        );
                    } else if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::PARCEL)) {
                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de paquetería",
                            "Hoy se entrega el paquete",
                            $request
                        );
                    } else if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::DRIVER)) {
                        $driverName = $request->requestDriver->driverRequestSchedule->driverSchedule->driver->full_name;
                        $car = $request->requestDriver->driverRequestSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de chofer",
                            "Chofer $driverName, Vehículo $car->trademark $car->model Placa $car->license_plate. ".
                            "Hora a recoger {$startDate->format('g:i A')}",
                            $request
                        );
                    } else if ($typeRequestCode === TypeRequestLookup::code(TypeRequestLookup::CAR)) {
                        $car = $request->requestCar->carRequestSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de vehículo",
                            "Vehículo $car->trademark $car->model Placa $car->license_plate. Recoger a las ".
                            "{$startDate->format('g:i A')} en la Oficina {$request->requestCar->office->name}",
                            $request
                        );
                    }

                    return Utils::createSummaryOfDayObject('','', new Request());
                });
        } else {
            return collect();
        }
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