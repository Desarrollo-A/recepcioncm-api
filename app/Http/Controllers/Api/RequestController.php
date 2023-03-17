<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Contracts\Services\ScoreServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\Request\StarRatingRequest;
use App\Http\Resources\Request\RequestResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
            ->only('deleteRequestRoom', 'starRatingRequest', 'deleteRequestPackage', 'deleteRequestDriver');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST.','.NameRole::APPLICANT)
            ->only('show');

        $this->requestService = $requestService;
        $this->notificationService = $notificationService;
        $this->scoreService = $scoreService;
    }

    public function show(int $id): JsonResponse
    {
        $request = $this->requestService->findById($id);
        return $this->showOne(new RequestResource($request));
    }

    public function deleteRequestRoom(int $id): Response
    {
        $request = $this->requestService->deleteRequestRoom($id, auth()->id());
        $this->notificationService->newToDeletedRequestRoomNotification($request);
        return $this->noContentResponse();
    }

    public function deleteRequestPackage(int $requestId): Response
    {
        $package = $this->requestService->deleteRequestPackage($requestId, auth()->id());
        $this->notificationService->deleteRequestPackageNotification($package);
        return $this->noContentResponse();
    }

    public function deleteRequestDriver(int $id): Response
    {
        $requestDriver = $this->requestService->deleteRequestDriver($id, auth()->id());
        $this->notificationService->deleteRequestDriverNotification($requestDriver);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function starRatingRequest(StarRatingRequest $request): Response
    {
        $dto = $request->toDTO();
        $this->scoreService->create($dto);
        return $this->noContentResponse();
    }
}
