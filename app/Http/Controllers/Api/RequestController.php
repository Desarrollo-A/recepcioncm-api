<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Contracts\Services\ScoreServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\Request\ResponseRejectRequestRequest;
use App\Http\Requests\Request\StarRatingRequest;
use App\Http\Resources\Request\RequestResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class RequestController extends BaseApiController
{
    private $requestService;
    private $notificationService;
    private $scoreService;

    public function __construct(RequestServiceInterface $requestService,
                                NotificationServiceInterface $notificationService,
                                ScoreServiceInterface $scoreService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('deleteRequestRoom', 'starRatingRequest', 'deleteRequestPackage');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST.','.NameRole::APPLICANT)
            ->only('show');
        $this->middleware('role.permission:'.NameRole::ADMIN)
            ->only('expiredRequest', 'finishedRequest');

        $this->requestService = $requestService;
        $this->notificationService = $notificationService;
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

    public function deleteRequestPackage(int $requestId): JsonResponse
    {
        $package = $this->requestService->deleteRequestPackage($requestId, auth()->user()->id);
        $this->notificationService->deleteRequestPackageNotification($package);
        return $this->noContentResponse();
    }

    public function deleteRequestDriver(int $id): JsonResponse
    {
        $requestDriver = $this->requestService->deleteRequestDriver($id, auth()->user()->id);
        $this->notificationService->deleteRequestDriverNotification($requestDriver);
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

    public function expiredRequest(): JsonResponse
    {
        $this->requestService->changeToExpired();
        return $this->noContentResponse();
    }

    public function finishedRequest(): JsonResponse
    {
        $requests = $this->requestService->changeToFinished();
        if ($requests->count() > 0) {
            $this->notificationService->createScoreRequestNotification($requests);
        }
        return $this->noContentResponse();
    }
    
}
