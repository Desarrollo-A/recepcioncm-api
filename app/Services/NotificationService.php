<?php

namespace App\Services;

use App\Contracts\Repositories\ActionRequestNotificationRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\RequestNotificationRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Utils;
use App\Models\Dto\ActionRequestNotificationDTO;
use App\Models\Dto\NotificationDTO;
use App\Models\Dto\RequestNotificationDTO;
use App\Models\Enums\Lookups\ActionRequestNotificationLookup;
use App\Models\Enums\Lookups\NotificationColorLookup;
use App\Models\Enums\Lookups\NotificationIconLookup;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\TypeNotificationsLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\PathRouteRecepcionist;
use App\Models\Enums\TypeLookup;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Request;
use App\Models\RequestCar;
use App\Models\RequestDriver;
use App\Models\RequestRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService extends BaseService implements NotificationServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $requestRepository;
    protected $requestNotificationRepository;
    protected $actionRequestNotificationRepository;
    protected $userRepository;

    private $requestNotificationService;
    private $userService;

    public function __construct(
        NotificationRepositoryInterface              $notificationRepository,
        LookupRepositoryInterface                    $lookupRepository,
        RequestRepositoryInterface                   $requestRepository,
        ActionRequestNotificationRepositoryInterface $actionRequestNotificationRepository,
        RequestNotificationRepositoryInterface       $requestNotificationRepository,
        UserRepositoryInterface                      $userRepository,
        RequestNotificationServiceInterface          $requestNotificationService,
        UserServiceInterface                         $userService
    )
    {
        $this->entityRepository = $notificationRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestRepository = $requestRepository;
        $this->actionRequestNotificationRepository = $actionRequestNotificationRepository;
        $this->requestNotificationRepository = $requestNotificationRepository;
        $this->userRepository = $userRepository;
        $this->requestNotificationService = $requestNotificationService;
        $this->userService = $userService;
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestRoomNotification(RequestRoom $requestRoom): void
    {
        $ids = $this->getRecepcionistIds($requestRoom->room->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::ROOM));

        foreach ($ids as $id) {
            $notification = $this->createNotification(
                "Nueva solicitud de sala {$requestRoom->request->code}",
                $id,
                TypeNotificationsLookup::ROOM,
                NotificationColorLookup::BLUE,
                NotificationIconLookup::ROOM
            );
            $this->requestNotificationService->create($requestRoom->request_id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    public function getAllNotificationLast5Days(int $userId): Collection
    {
        return $this->entityRepository->getAllNotificationLast5Days($userId);
    }

    /**
     * @throws CustomErrorException
     */
    public function newOrResponseToApprovedRequestRoomNotification(Request $request): void
    {
        $notification = $this->createNotification(
            "La solicitud de sala $request->code fue aprobada",
            $request->user_id,
            TypeNotificationsLookup::ROOM,
            NotificationColorLookup::GREEN,
            NotificationIconLookup::ROOM
        );

        $this->requestNotificationService->create($request->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function newToProposalRequestRoomNotification(Request $request): void
    {
        $notification = $this->createNotification(
            "Propuesta de la solicitud de sala $request->code",
            $request->user_id,
            TypeNotificationsLookup::ROOM,
            NotificationColorLookup::ORANGE,
            NotificationIconLookup::ROOM
        );

        $this->requestNotificationService->create($request->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function newToDeletedRequestRoomNotification(Request $request): void
    {
        $ids = $this->getRecepcionistIds($request->requestRoom->room->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::ROOM));

        foreach($ids as $id) {
            $notification = $this->createNotification(
                "La solicitud de sala $request->code fue eliminada",
                $id,
                TypeNotificationsLookup::ROOM,
                NotificationColorLookup::RED, NotificationIconLookup::ROOM
            );

            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedToCancelledRequestRoomNotification(Request $request, User $user): void
    {
        if ($user->role->name === NameRole::RECEPCIONIST) {
            $notification = $this->createNotification(
                "La solicitud de sala $request->code fue cancelada",
                $request->user_id,
                TypeNotificationsLookup::ROOM,
                NotificationColorLookup::RED,
                NotificationIconLookup::ROOM
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }

        if ($user->role->name === NameRole::APPLICANT) {
            $ids = $this->getRecepcionistIds($request->requestRoom->room->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::ROOM));

            foreach($ids as $id) {
                $notification = $this->createNotification(
                    "La solicitud de sala $request->code fue cancelada",
                    $id,
                    TypeNotificationsLookup::ROOM,
                    NotificationColorLookup::RED,
                    NotificationIconLookup::ROOM
                );
                $this->requestNotificationService->create($request->id, $notification->id);
                Utils::eventAlertNotification($notification);
            }
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalToRejectedOrResponseRequestRoomNotification(Request $request): void
    {
        $message = '';
        $color = '';
        if ($request->status->code === StatusRoomRequestLookup::code(StatusRoomRequestLookup::REJECTED)) {
            $message = "Propuesta de la solicitud de sala $request->code fue rechazada";
            $color = NotificationColorLookup::RED;
        } else if ($request->status->code === StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW)) {
            $message = "Propuesta de la solicitud de sala $request->code fue aceptada";
            $color = NotificationColorLookup::GREEN;
        }

        $ids = $this->getRecepcionistIds($request->requestRoom->room->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::ROOM));
        foreach($ids as $id) {
            $notification = $this->createNotification(
                $message, 
                $id, 
                TypeNotificationsLookup::ROOM,
                $color, 
                NotificationIconLookup::ROOM
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function readNotification(int $id): void
    {
        $dto = new NotificationDTO(['is_read' => true]);
        $this->entityRepository->update($id, $dto->toArray(['is_read']));
    }

    /**
     * @throws CustomErrorException
     */
    public function readAllNotificationUser(int $userId): void
    {
        $dto = new NotificationDTO(['is_read' => true]);
        $this->entityRepository->massiveNotificationUserUpdate($userId, $dto->toArray(['is_read']));
    }

    /**
     * @throws CustomErrorException
     */
    public function createConfirmNotification(): void
    {
        $this->requestRepository->getApprovedRequestsTomorrow()->each(function (Request $request) {
            switch ($request->type_code) {
                case TypeRequestLookup::code(TypeRequestLookup::ROOM):
                    $typeNotification = TypeNotificationsLookup::ROOM;
                    $typeRequest = TypeRequestLookup::ROOM;
                    break;
                case TypeRequestLookup::code(TypeRequestLookup::PARCEL):
                    $typeNotification = TypeNotificationsLookup::PARCEL;
                    $typeRequest = TypeRequestLookup::PARCEL;
                    break;
                case TypeRequestLookup::code(TypeRequestLookup::DRIVER):
                    $typeNotification = TypeNotificationsLookup::DRIVER;
                    $typeRequest = TypeRequestLookup::DRIVER;
                    break;
                case TypeRequestLookup::code(TypeRequestLookup::CAR):
                    $typeNotification = TypeNotificationsLookup::CAR;
                    $typeRequest = TypeRequestLookup::CAR;
                    break;
                default:
                    $typeNotification = TypeNotificationsLookup::GENERAL;
                    $typeRequest = '';
            }

            $notification = $this->createNotification("Confirmación de solicitud de $typeRequest $request->code", $request->user_id,
                $typeNotification, NotificationColorLookup::BLUE, NotificationIconLookup::CONFIRM);
            $this->createActionNotification($notification, $request, ActionRequestNotificationLookup::CONFIRM);
        });

        $this->actionRequestNotificationRepository->updatePastRecords();
    }

    /**
     * @throws CustomErrorException
     */
    public function createScoreRequestNotification(Collection $requests): void
    {
        $requests->each(function (Request $request) {
            switch ($request->type_code) {
                case TypeRequestLookup::code(TypeRequestLookup::ROOM):
                    $typeNotification = TypeNotificationsLookup::ROOM;
                    $typeRequest = TypeRequestLookup::ROOM;
                    break;
                case TypeRequestLookup::code(TypeRequestLookup::DRIVER):
                    $typeNotification = TypeNotificationsLookup::DRIVER;
                    $typeRequest = TypeRequestLookup::DRIVER;
                    break;
                case TypeRequestLookup::code(TypeRequestLookup::CAR):
                    $typeNotification = TypeNotificationsLookup::CAR;
                    $typeRequest = TypeRequestLookup::CAR;
                    break;
                default:
                    $typeNotification = TypeNotificationsLookup::GENERAL;
                    $typeRequest = '';
            }

            $notification = $this->createNotification("Calificar solicitud de $typeRequest $request->code", $request->user_id,
                $typeNotification, NotificationColorLookup::BLUE, NotificationIconLookup::STAR);
            $this->createActionNotification($notification, $request, ActionRequestNotificationLookup::SCORE);
        });
    }

    /**
     * @throws CustomErrorException
     */
    public function minimumStockNotification(Inventory $inventory): void
    {
        $ids = $this->getRecepcionistIds($inventory->office_id, PathRouteRecepcionist::INVENTORY);

        foreach($ids as $id) {
            $notification = $this->createNotification(
                "El inventario $inventory->code se encuentra al mínimo",
                $id,
                TypeNotificationsLookup::INVENTORY,
                NotificationColorLookup::YELLOW,
                NotificationIconLookup::WARNING
            );
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestPackageNotification(Package $package): void
    {
        $notification = $this->createNotification(
            "Solicitud de paquetería {$package->request->code} para revisión",
            $package->request->user->department_manager_id,
            TypeNotificationsLookup::PARCEL,
            NotificationColorLookup::BLUE,
            NotificationIconLookup::TRUCK
        );
        $this->requestNotificationService->create($package->request_id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function deleteRequestPackageNotification (Package $package): void
    {
        if ($package->request->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW_MANAGER)) {
            $notificationDelete = $this->createNotification(
                "La solicitud de paquetería {$package->request->code} fue eliminada",
                $package->request->user->department_manager_id,
                TypeNotificationsLookup::PARCEL,
                NotificationColorLookup::RED,
                NotificationIconLookup::TRUCK
            );
            Utils::eventAlertNotification($notificationDelete);
        }

        if ($package->request->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW)) {
            $ids = $this->getRecepcionistIds($package->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::PARCEL));

            foreach($ids as $id) {
                $notificationDelete = $this->createNotification(
                    "La solicitud de paquetería {$package->request->code} fue eliminada",
                    $id,
                    TypeNotificationsLookup::PARCEL,
                    NotificationColorLookup::RED,
                    NotificationIconLookup::TRUCK
                );
                Utils::eventAlertNotification($notificationDelete);
            }
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequestPackageNotification(Request $request, User $user, int $driverId = null): void
    {
        if ($user->role->name === NameRole::RECEPCIONIST || $user->role->name === NameRole::DEPARTMENT_MANAGER) {
            $notification = $this->createNotification(
                "La solicitud de paquetería $request->code fue cancelada",
                $request->user_id,
                TypeNotificationsLookup::PARCEL,
                NotificationColorLookup::RED,
                NotificationIconLookup::TRUCK
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }

        if ($user->role->name === NameRole::APPLICANT) {
            $ids = $this->getRecepcionistIds($request->package->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::PARCEL));

            foreach($ids as $id) {
                $notification = $this->createNotification(
                    "La solicitud de paquetería $request->code fue cancelada",
                    $id,
                    TypeNotificationsLookup::PARCEL,
                    NotificationColorLookup::RED,
                    NotificationIconLookup::TRUCK
                );
                $this->requestNotificationService->create($request->id, $notification->id);
                Utils::eventAlertNotification($notification);
            }
        }

        if (!is_null($driverId)) {
            $notification = $this->createNotification(
                "La solicitud de paquetería $request->code fue cancelada",
                $driverId,
                TypeNotificationsLookup::PARCEL,
                NotificationColorLookup::RED,
                NotificationIconLookup::TRUCK
            );
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function transferPackageRequestNotification(Package $package): void
    {
        $ids = $this->getRecepcionistIds($package->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::PARCEL));

        foreach($ids as $id) {
            $notification = $this->createNotification(
                "La solicitud de paquetería {$package->request->code} fue transferida",
                $id,
                TypeNotificationsLookup::PARCEL,
                NotificationColorLookup::BLUE,
                NotificationIconLookup::TRUCK
            );

            $this->requestNotificationService->create($package->request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedPackageRequestNotification(Package $package, int $driverId = null): void
    {
        $notification = $this->createNotification(
            "La solicitud de paquetería {$package->request->code} fue aprobada",
            $package->request->user_id,
            TypeNotificationsLookup::PARCEL,
            NotificationColorLookup::GREEN,
            NotificationIconLookup::TRUCK
        );
        $this->requestNotificationService->create($package->request->id, $notification->id);
        Utils::eventAlertNotification($notification);

        if (!is_null($driverId)) {
            $notification = $this->createNotification("La solicitud de paquetería {$package->request->code} fue aprobada",
                $driverId, TypeNotificationsLookup::PARCEL, NotificationColorLookup::GREEN, NotificationIconLookup::TRUCK);
            $this->requestNotificationService->create($package->request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function onRoadPackageRequestNotification(Request $request): void
    {
        $notification = $this->createNotification(
            "El paquete de la solicitud $request->code está en camino",
            $request->user_id,
            TypeNotificationsLookup::PARCEL,
            NotificationColorLookup::BLUE,
            NotificationIconLookup::TRUCK
        );
        $this->requestNotificationService->create($request->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function deliveredPackageRequestNotification(Request $request): void
    {
        $notification = $this->createNotification(
            "El paquete de la solicitud $request->code fue entregado",
            $request->user_id,
            TypeNotificationsLookup::PARCEL,
            NotificationColorLookup::GREEN,
            NotificationIconLookup::BOX
        );
        $this->requestNotificationService->create($request->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalPackageRequestNotification(Request $requestPackageProposal): void
    {
        $notification = $this->createNotification(
            "Propuesta de la solicitud de paquetería $requestPackageProposal->code",
            $requestPackageProposal->user_id,
            TypeNotificationsLookup::PARCEL,
            NotificationColorLookup::ORANGE,
            NotificationIconLookup::BOX
        );
        $this->requestNotificationService->create($requestPackageProposal->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectPackageRequestNotification(Request $request): void
    {
        $messageNotification = '';
        $colorNotification = '';
        $ids = $this->getRecepcionistIds($request->package->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::PARCEL));
        if ($request->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW) ||
            $request->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED)) {
            $messageNotification = "Propuesta de la solicitud de paquetería $request->code fue aceptada";
            $colorNotification = NotificationColorLookup::GREEN;
        } else if ($request->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::REJECTED)) {
            $messageNotification = "Propuesta de la solicitud de paquetería $request->code fue rechazada";
            $colorNotification = NotificationColorLookup::RED;
        }

        foreach($ids as $id) {
            $notification = $this->createNotification(
                $messageNotification,
                $id,
                TypeNotificationsLookup::PARCEL,
                $colorNotification,
                NotificationIconLookup::BOX
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestDriverNotification(RequestDriver $requestDriver): void
    {
        $ids = $this->getRecepcionistIds($requestDriver->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::DRIVER));

        foreach($ids as $id) {
            $notification = $this->createNotification(
                "Nueva solicitud de chofer {$requestDriver->request->code}",
                $id,
                TypeNotificationsLookup::DRIVER,
                NotificationColorLookup::BLUE,
                NotificationIconLookup::DRIVER
            );
            $this->requestNotificationService->create($requestDriver->request_id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function deleteRequestDriverNotification(RequestDriver $requestDriver): void
    {
        $ids = $this->getRecepcionistIds($requestDriver->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::DRIVER));

        foreach($ids as $id) {
            $notificationDelete = $this->createNotification(
                "La solicitud de chofer {$requestDriver->request->code} fue eliminada",
                $id,
                TypeNotificationsLookup::DRIVER,
                NotificationColorLookup::RED,
                NotificationIconLookup::DRIVER
            );
            Utils::eventAlertNotification($notificationDelete);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequestDriverNotification(RequestDriver $requestDriver, User $user, int $driverId = null): void
    {
        if ($user->role->name === NameRole::RECEPCIONIST) {
            $notification = $this->createNotification(
                "La solicitud de chofer $requestDriver->request->code fue cancelada",
                $requestDriver->request->user_id,
                TypeNotificationsLookup::DRIVER,
                NotificationColorLookup::RED,
                NotificationIconLookup::DRIVER
            );
            $this->requestNotificationService->create($requestDriver->request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }

        if ($user->role->name === NameRole::APPLICANT) {
            $ids = $this->getRecepcionistIds($requestDriver->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::DRIVER));

            foreach($ids as $id) {
                $notification = $this->createNotification(
                    "La solicitud de chofer $requestDriver->request->code fue cancelada",
                    $id,
                    TypeNotificationsLookup::DRIVER,
                    NotificationColorLookup::RED,
                    NotificationIconLookup::DRIVER
                );
                $this->requestNotificationService->create($requestDriver->request->id, $notification->id);
                Utils::eventAlertNotification($notification);
            }
        }

        if (!is_null($driverId)) {
            $notification = $this->createNotification("La solicitud de chofer {$requestDriver->request->code} fue cancelada", $driverId,
                TypeNotificationsLookup::DRIVER, NotificationColorLookup::RED, NotificationIconLookup::DRIVER);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequestDriverNotification(RequestDriver $requestDriver): void
    {
        $ids = $this->getRecepcionistIds($requestDriver->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::DRIVER));

        foreach($ids as $id) {
            $notification = $this->createNotification(
                "La solicitud de chofer {$requestDriver->request->code} fue transferida",
                $id,
                TypeNotificationsLookup::DRIVER,
                NotificationColorLookup::BLUE,
                NotificationIconLookup::DRIVER
            );
            $this->requestNotificationService->create($requestDriver->request_id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequestDriverNotification(Request $request, int $driverId = null): void
    {
        $notification = $this->createNotification(
            "La solicitud de chofer $request->code fue aprobada",
            $request->user_id,
            TypeNotificationsLookup::DRIVER,
            NotificationColorLookup::GREEN,
            NotificationIconLookup::DRIVER
        );
        $this->requestNotificationService->create($request->id, $notification->id);
        Utils::eventAlertNotification($notification);

        if (!is_null($driverId)) {
            $notification = $this->createNotification(
                "La solicitud de chofer $request->code fue aprobada",
                $driverId,
                TypeNotificationsLookup::DRIVER,
                NotificationColorLookup::GREEN,
                NotificationIconLookup::DRIVER
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalDriverRequestNotification(Request $proposalDriverRequest): void
    {
        $notification = $this->createNotification(
            "Propuesta de solicitud de chofer $proposalDriverRequest->code",
            $proposalDriverRequest->user_id,
            TypeNotificationsLookup::DRIVER,
            NotificationColorLookup::ORANGE,
            NotificationIconLookup::DRIVER
        );
        $this->requestNotificationService->create($proposalDriverRequest->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequestDriverNotification(Request $requestDriverResponseReject):void
    {
        $messageNotification = '';
        $colorNotification = '';
        $ids = $this->getRecepcionistIds(
            $requestDriverResponseReject->requestDriver->office_id,
            PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::DRIVER)
        );

        if($requestDriverResponseReject->status->code === StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED)){
            $messageNotification = "Propuesta de solicitud de chofer $requestDriverResponseReject->code fue aceptada";
            $colorNotification = NotificationColorLookup::GREEN;
        }
        if($requestDriverResponseReject->status->code === StatusDriverRequestLookup::code(StatusDriverRequestLookup::REJECTED)) {
            $messageNotification = "Propuesta de solicitud de chofer $requestDriverResponseReject->code fue rechazada";
            $colorNotification = NotificationColorLookup::RED;
        }

        foreach($ids as $id) {
            $notification = $this->createNotification(
                $messageNotification,
                $id,
                TypeNotificationsLookup::DRIVER,
                $colorNotification,
                NotificationIconLookup::DRIVER
            );
            $this->requestNotificationService->create($requestDriverResponseReject->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestCarNotification(RequestCar $requestCar): void
    {
        $ids = $this->getRecepcionistIds($requestCar->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::CAR));

        foreach($ids as $id) {
            $notification = $this->createNotification(
                "Nueva solicitud de vehículo {$requestCar->request->code}",
                $id,
                TypeNotificationsLookup::CAR,
                NotificationColorLookup::BLUE,
                NotificationIconLookup::CAR
            );
            $this->requestNotificationService->create($requestCar->request_id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function deleteRequestCarNotification(RequestCar $requestCar): void
    {
        $ids = $this->getRecepcionistIds($requestCar->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::CAR));

        foreach($ids as $id) {
            $notificationDelete = $this->createNotification(
                "La solicitud del vehículo {$requestCar->request->code} fue eliminada",
                $id,
                TypeNotificationsLookup::CAR,
                NotificationColorLookup::RED,
                NotificationIconLookup::CAR
            );
            Utils::eventAlertNotification($notificationDelete);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequestCarNotification(RequestCar $requestCar): void
    {
        $ids = $this->getRecepcionistIds($requestCar->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::CAR));

        foreach($ids as $id) {
            $notification = $this->createNotification(
                "La solicitud del vehículo {$requestCar->request->code} fue transferida",
                $id,
                TypeNotificationsLookup::CAR,
                NotificationColorLookup::BLUE,
                NotificationIconLookup::CAR
            );
            $this->requestNotificationService->create($requestCar->request_id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequestCarNotification(Request $request, User $user): void
    {
        if ($user->role->name === NameRole::RECEPCIONIST) {
            $notification = $this->createNotification(
                "La solicitud del vehículo $request->code fue cancelada",
                $request->user_id,
                TypeNotificationsLookup::CAR,
                NotificationColorLookup::RED,
                NotificationIconLookup::CAR
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }

        if ($user->role->name === NameRole::APPLICANT) {
            $ids = $this->getRecepcionistIds($request->requestCar->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::CAR));

            foreach($ids as $id) {
                $notification = $this->createNotification(
                    "La solicitud del vehículo $request->code fue cancelada",
                    $id,
                    TypeNotificationsLookup::CAR,
                    NotificationColorLookup::RED,
                    NotificationIconLookup::CAR
                );
                $this->requestNotificationService->create($request->id, $notification->id);
                Utils::eventAlertNotification($notification);
            }
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequestCarNotification(Request $request): void
    {
        $notification = $this->createNotification(
            "La solicitud del vehículo $request->code fue aprobada",
            $request->user_id,
            TypeNotificationsLookup::CAR,
            NotificationColorLookup::GREEN,
            NotificationIconLookup::CAR
        );
        $this->requestNotificationService->create($request->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
    * @throws CustomErrorException
    */
    public function proposalCarRequestNotification(Request $request): void
    {
        $notification = $this->createNotification(
            "Propuesta de la solicitud de vehículo $request->code",
            $request->user_id,
            TypeNotificationsLookup::CAR,
            NotificationColorLookup::ORANGE,
            NotificationIconLookup::CAR
        );
        $this->requestNotificationService->create($request->id, $notification->id);
        Utils::eventAlertNotification($notification);
    }

    /**
    * @throws CustomErrorException
    */
    public function responseRejectCarRequestNotification(Request $request): void
    {
        $messageNotification = '';
        $colorNotification = '';
        $ids = $this->getRecepcionistIds($request->requestCar->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::CAR));

        if($request->status->code === StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED)){
            $messageNotification = "Propuesta de la solicitud de vehículo $request->code fue aceptada";
            $colorNotification = NotificationColorLookup::GREEN;
        }else if($request->status->code === StatusCarRequestLookup::code(StatusCarRequestLookup::REJECTED)) {
            $messageNotification = "Propuesta de la solicitud de vehículo $request->code fue rechazada";
            $colorNotification = NotificationColorLookup::RED;
        }

        foreach($ids as $id) {
            $notification = $this->createNotification(
                $messageNotification,
                $id,
                TypeNotificationsLookup::CAR,
                $colorNotification,
                NotificationIconLookup::CAR
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function acceptOrCancelPackageRequestNotification(Request $request): void
    {
        if ($request->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW)) {
            $message = "Nueva solicitud de paquetería $request->code";
            $colorNotification = NotificationColorLookup::BLUE;
            $ids = $this->getRecepcionistIds($request->package->office_id, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::PARCEL));

            foreach($ids as $id) {
                $notification = $this->createNotification(
                    $message,
                    $id,
                    TypeNotificationsLookup::PARCEL,
                    $colorNotification,
                    NotificationIconLookup::TRUCK
                );
                $this->requestNotificationService->create($request->id, $notification->id);
                Utils::eventAlertNotification($notification);
            }
        }

        if ($request->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)) {
            $message = "La solicitud de paquetería $request->code fue cancelada";
            $colorNotification = NotificationColorLookup::RED;

            $notification = $this->createNotification(
                $message,
                $request->user_id,
                TypeNotificationsLookup::PARCEL,
                $colorNotification,
                NotificationIconLookup::TRUCK
            );
            $this->requestNotificationService->create($request->id, $notification->id);
            Utils::eventAlertNotification($notification);
        }
    }

    /**
     * @throws CustomErrorException
     */
    private function createActionNotification(Notification $notification, Request $request, string $lookup): void
    {
        $requestNotificationDTO = new RequestNotificationDTO([
            'notification_id' => $notification->id,
            'request_id' => $request->id
        ]);
        $requestNotification = $this->requestNotificationRepository->create($requestNotificationDTO->toArray([
            'notification_id', 'request_id'
        ]));
        $actionRequestNotificationDTO = new ActionRequestNotificationDTO([
            'request_notification_id' => $requestNotification->id,
            'type_id' => $this->lookupRepository->findByCodeAndType(
                ActionRequestNotificationLookup::code($lookup),
                TypeLookup::ACTION_REQUEST_NOTIFICATION)->id
        ]);
        $this->actionRequestNotificationRepository->create($actionRequestNotificationDTO->toArray([
            'request_notification_id', 'type_id'
        ]));

        Utils::eventAlertNotification($notification);
    }

    private function getRecepcionistIds(int $officeId, string $pathUrl): array
    {
        return $this->userService->getRecepcionistByPermission($officeId, $pathUrl)
            ->pluck('id')
            ->toArray();
    }

    /**
     * @throws CustomErrorException
     */
    private function createNotification(string $message, int $userId, string $type, string $color, string $icon): Notification
    {
        $dto = new NotificationDTO([
            'message' => $message,
            'user_id' => $userId,
            'type_id' => $this->getTypeId($type),
            'color_id' => $this->getColorId($color),
            'icon_id' => $this->getIconId($icon)
        ]);
        return $this->entityRepository->create($dto->toArray(['message', 'user_id', 'type_id', 'color_id', 'icon_id']));
    }

    private function getColorId(string $value): int
    {
        return $this->lookupRepository->findByCodeAndType(NotificationColorLookup::code($value),
            TypeLookup::NOTIFICATION_COLOR)->id;
    }

    private function getTypeId(string $value): int
    {
        return $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code($value),
            TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id;
    }

    private function getIconId(string $value): int
    {
        return $this->lookupRepository->findByCodeAndType(NotificationIconLookup::code($value),
            TypeLookup::NOTIFICATION_ICON)->id;
    }
}