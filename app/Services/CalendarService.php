<?php

namespace App\Services;

use App\Contracts\Repositories\RequestRepositoryInterface;
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
    protected $requestRepository;

    public function __construct(RequestRepositoryInterface $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function getDataCalendar(User $user): Collection
    {
        $roleName = $user->role->name;
        if ($roleName === NameRole::RECEPCIONIST) {
            return $this->requestRepository->getAllApprovedRecepcionistWithStartDateCondition($user->office_id, '>=')
                ->map(function (Request $request) {
                    $typeCodeRequest = $request->type->code;

                    if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::ROOM)) {
                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de sala. Sala {$request->requestRoom->room->name} - ".
                            "Tipo {$request->type->name}",
                            $request);
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::PARCEL)) {
                        $driverName = $request->package->driverPackageSchedule->driverSchedule->driver->full_name;
                        $car = $request->package->driverPackageSchedule->carSchedule->car;

                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de paquetería. Chofer $driverName, Vehículo $car->trademark ".
                            "$car->model Placa $car->license_plate",
                            $request);
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::DRIVER)) {
                        $driverName = $request->requestDriver->driverRequestSchedule->driverSchedule->driver->full_name;
                        $car = $request->requestDriver->driverRequestSchedule->carSchedule->car;

                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de chofer. Chofer $driverName, Vehículo $car->trademark ".
                            "$car->model Placa $car->license_plate",
                            $request);
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::CAR)) {
                        $car = $request->requestCar->carRequestSchedule->carSchedule->car;

                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de vehículo. Vehículo $car->trademark $car->model Placa $car->license_plate",
                            $request);
                    }

                    return Utils::createEventCalendarObject("", $request);
                });
        }
        if ($roleName === NameRole::APPLICANT) {
            return $this->requestRepository->getAllApprovedApplicantWithStartDateCondition($user->id, '>=')
                ->map(function (Request $request) {
                    $typeCodeRequest = $request->type->code;

                    if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::ROOM)) {
                        $roomName = $request->requestRoom->room->name;
                        $officeName = $request->requestRoom->room->office->name;

                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de sala. Oficina $officeName, Sala $roomName - Tipo {$request->type->name}",
                            $request);
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::PARCEL)) {
                        $driverName = $request->package->driverPackageSchedule->driverSchedule->driver->full_name;
                        $car = $request->package->driverPackageSchedule->carSchedule->car;

                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de paquetería. Chofer $driverName, Vehículo $car->trademark ".
                            "$car->model Placa $car->license_plate",
                            $request);
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::DRIVER)) {
                        $driverName = $request->requestDriver->driverRequestSchedule->driverSchedule->driver->full_name;
                        $car = $request->requestDriver->driverRequestSchedule->carSchedule->car;

                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de chofer. Chofer $driverName, Vehículo $car->trademark ".
                            "$car->model Placa $car->license_plate",
                            $request);
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::CAR)) {
                        $car = $request->requestCar->carRequestSchedule->carSchedule->car;

                        return Utils::createEventCalendarObject(
                            "Solicitud $request->code de vehículo. Vehículo $car->trademark $car->model Placa $car->license_plate",
                            $request);
                    }

                    return Utils::createEventCalendarObject("", $request);
                });
        }

        return collect();
    }

    public function getSummaryOfDay(User $user): Collection
    {
        $roleName = $user->role->name;
        if ($roleName === NameRole::RECEPCIONIST) {
            return $this->requestRepository->getAllApprovedRecepcionistWithStartDateCondition($user->office_id)
                ->map(function (Request $request) {
                    $startDate = new Carbon($request->start_date);
                    $endDate = new Carbon($request->end_date);
                    $typeCodeRequest = $request->type->code;

                    if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::ROOM)) {
                        $roomName = $request->requestRoom->room->name;
                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de sala",
                            "{$startDate->format('g:i A')} - {$endDate->format('g:i A')}, Sala $roomName",
                            $request
                        );
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::PARCEL)) {
                        $driverName = $request->package->driverPackageSchedule->driverSchedule->driver->full_name;
                        $car = $request->package->driverPackageSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de paquetería",
                            "Chofer $driverName, Vehículo $car->trademark $car->model Placa $car->license_plate",
                            $request
                        );
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::DRIVER)) {
                        $driverName = $request->requestDriver->driverRequestSchedule->driverSchedule->driver->full_name;
                        $car = $request->requestDriver->driverRequestSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de chofer",
                            "Chofer $driverName, Vehículo $car->trademark $car->model Placa $car->license_plate. ".
                            "Recoger a las {$startDate->format('g:i A')}",
                            $request
                        );
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::CAR)) {
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
        }
        if ($roleName === NameRole::APPLICANT) {
            return $this->requestRepository->getAllApprovedApplicantWithStartDateCondition($user->id)
                ->map(function (Request $request) {
                    $startDate = new Carbon($request->start_date);
                    $endDate = new Carbon($request->end_date);
                    $typeCodeRequest = $request->type->code;

                    if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::ROOM)) {
                        $room = $request->requestRoom->room;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de sala",
                            "{$startDate->format('g:i A')} - {$endDate->format('g:i A')}, ".
                            "Oficina {$room->office->name} en Sala $room->name",
                            $request
                        );
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::PARCEL)) {
                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de paquetería",
                            "Hoy se entrega el paquete",
                            $request
                        );
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::DRIVER)) {
                        $driverName = $request->requestDriver->driverRequestSchedule->driverSchedule->driver->full_name;
                        $car = $request->requestDriver->driverRequestSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de chofer",
                            "Chofer $driverName, Vehículo $car->trademark $car->model Placa $car->license_plate. ".
                            "Hora a recoger {$startDate->format('g:i A')}",
                            $request
                        );
                    } else if ($typeCodeRequest === TypeRequestLookup::code(TypeRequestLookup::CAR)) {
                        $car = $request->requestCar->carRequestSchedule->carSchedule->car;

                        return Utils::createSummaryOfDayObject(
                            "Solicitud $request->code de vehículo",
                            "Vehículo $car->trademark $car->model Placa $car->license_plate. Recoger a las ".
                            "{$startDate->format('g:i A')} en la Oficina {$request->requestCar->office->name}",
                            $request
                        );
                    }

                    return Utils::createSummaryOfDayObject('','', $request);
                });
        }

        return collect();
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