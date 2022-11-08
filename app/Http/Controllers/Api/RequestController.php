<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Contracts\Services\ScoreServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Utils;
use App\Http\Requests\Request\ResponseRejectRequestRequest;
use App\Http\Requests\Request\StarRatingRequest;
use App\Http\Resources\Request\RequestResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class RequestController extends BaseApiController
{
    private $requestService;
    private $notificationService;
    private $requestNotificationService;
    private $scoreService;

    public function __construct(RequestServiceInterface $requestService,
                                NotificationServiceInterface $notificationService,
                                RequestNotificationServiceInterface $requestNotificationService,
                                ScoreServiceInterface $scoreService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('responseRejectRequest', 'deleteRequestRoom', 'starRatingRequest');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST.','.NameRole::APPLICANT)
            ->only('show');

        $this->requestService = $requestService;
        $this->notificationService = $notificationService;
        $this->requestNotificationService = $requestNotificationService;
        $this->scoreService = $scoreService;
    }

    public function show(int $id): JsonResponse
    {
        $request = $this->requestService->findById($id);
        return $this->showOne(new RequestResource($request));
    }

    public function deleteRequestRoom(int $id): JsonResponse
    {
        $request = $this->requestService->deleteRequestRoom($id, auth()->id());
        $this->notificationService->newToDeletedRequestRoomNotification($request);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $id, ResponseRejectRequestRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestModel = $this->requestService->responseRejectRequest($id, $dto);
        $notification = $this->notificationService->proposalToRejectedOrResponseRequestRoomNotification($requestModel);
        $this->requestNotificationService->create($requestModel->id, $notification->id);
        Utils::eventAlertNotification($notification);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function starRatingRequest(StarRatingRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->scoreService->create($dto);
        return $this->noContentResponse();
    }
}
