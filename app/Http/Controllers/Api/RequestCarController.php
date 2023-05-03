<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\MovementRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestCarServiceInterface;
use App\Contracts\Services\RequestEmailServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\CancelRequest\CancelRequest;
use App\Http\Requests\Request\ResponseRejectRequest;
use App\Http\Requests\RequestCar\AddExtraInformationCarRequest;
use App\Http\Requests\RequestCar\ApprovedCarRequest;
use App\Http\Requests\RequestCar\ProposalCarRequest;
use App\Http\Requests\RequestCar\StoreRequestCarRequest;
use App\Http\Requests\RequestCar\TransferCarRequest;
use App\Http\Requests\RequestCar\UploadImagesFilesRequest;
use App\Http\Requests\RequestCar\UploadZipImagesCarRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\RequestCar\RequestCarResource;
use App\Http\Resources\RequestCar\RequestCarViewCollection;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestCarController extends BaseApiController
{
    private $requestCarService;
    private $notificationService;
    private $requestEmailService;
    private $movementRequestService;

    public function __construct(
        RequestCarServiceInterface $requestCarService,
        NotificationServiceInterface $notificationService,
        RequestEmailServiceInterface $requestEmailService,
        MovementRequestServiceInterface $movementRequestService
    )
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'deleteRequestCar', 'responseRejectRequest');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'store', 'show', 'cancelRequest', 'getStatusByStatusCurrent');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('transferRequest', 'approvedRequest', 'getBusyDaysForProposalCalendar', 'proposalRequest',
                'addExtraCarInformation', 'uploadImagesFiles');

        $this->requestCarService = $requestCarService;
        $this->notificationService = $notificationService;
        $this->requestEmailService = $requestEmailService;
        $this->movementRequestService = $movementRequestService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $requestCars = $this->requestCarService->findAllCarsPaginated($request, $user);
        return $this->showAll(new RequestCarViewCollection($requestCars, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestCarRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestCar = $this->requestCarService->create($dto);
        return $this->showOne(new RequestCarResource($requestCar));
    }

    public function deleteRequestCar(int $requestId): Response
    {
        $requestCar = $this->requestCarService->deleteRequestCar($requestId, auth()->user());
        $this->notificationService->deleteRequestCarNotification($requestCar);
        return $this->noContentResponse();
    }

    public function show(int $requestId): JsonResponse
    {
        $requestCar = $this->requestCarService->findByRequestId($requestId, auth()->user());
        return $this->showOne(new RequestCarResource($requestCar));
    }

    public function getStatusByStatusCurrent(string $code): JsonResponse
    {
        $roleName = auth()->user()->role->name;
        $status = $this->requestCarService->getStatusByStatusCurrent($code, $roleName);
        return $this->showAll(LookupResource::collection($status));
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $requestCarId, TransferCarRequest $request): Response
    {
        $requestCar = $this->requestCarService->transferRequest($requestCarId, $request->toDTO());
        $this->notificationService->transferRequestCarNotification($requestCar);
        $this->movementRequestService->create($requestCar->request_id, auth()->id(), 'Transferencia de solicitud');
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequest $request): Response
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $data = $this->requestCarService->cancelRequest($dto);
        $this->notificationService->cancelRequestCarNotification($data->request, auth()->user());
        if ($data->previouslyApproved) {
            $this->requestEmailService->sendCancelledRequestCarMail($data->request);
        }
        $this->movementRequestService->create($requestId, auth()->id(), 'Solicitud cancelada');
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(ApprovedCarRequest $request): Response
    {
        $dto = $request->toDTO();
        $requestModel = $this->requestCarService->approvedRequest($dto);
        $this->notificationService->approvedRequestCarNotification($requestModel);
        $this->requestEmailService->sendApprovedRequestCarMail($requestModel);
        $this->movementRequestService->create($requestModel->id, auth()->id(), 'Solicitud aprobada');
        return $this->noContentResponse();
    }

    public function getBusyDaysForProposalCalendar(): JsonResponse
    {
        $data = $this->requestCarService->getBusyDaysForProposalCalendar();
        return $this->showAll($data);
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(ProposalCarRequest $request): Response
    {
        $dto = $request->toDTO();
        $requestModel = $this->requestCarService->proposalRequest($dto);
        $this->notificationService->proposalCarRequestNotification($requestModel);
        $this->movementRequestService->create($requestModel->id, auth()->id(), 'Propuesta de solicitud');
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $requestId, ResponseRejectRequest $request): Response
    {
        $dto = $request->toDTO();
        $requestModel = $this->requestCarService->responseRejectRequest($requestId, $dto);
        $this->notificationService->responseRejectCarRequestNotification($requestModel);

        if ($request->status->code === StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED)) {
            $this->movementRequestService->create($requestModel->id, auth()->id(), 'Solicitud aprobada');
        } else if($request->status->code === StatusCarRequestLookup::code(StatusCarRequestLookup::REJECTED)) {
            $this->movementRequestService->create($requestModel->id, auth()->id(), 'Solicitud rechazada');
        }

        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function addExtraCarInformation(int $id, AddExtraInformationCarRequest $request): Response
    {
        $dto = $request->toDTO();
        $requestCar = $this->requestCarService->addExtraCarInformation($id, $dto);
        $this->movementRequestService->create($requestCar->request_id, auth()->id(), 'Se agrega información extra a la solicitud');
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadImagesFiles(int $id, UploadImagesFilesRequest $request): Response
    {
        $dto = $request->toDTO();
        $this->requestCarService->uploadImagesFiles($id, $dto);
        $requestCar = $this->requestCarService->findById($id);
        $this->movementRequestService->create($requestCar->request_id, auth()->id(), 'Se suben imágenes del vehículo');
        return $this->noContentResponse();
    }
}
