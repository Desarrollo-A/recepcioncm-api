<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\LookupServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\Request\ResponseRejectRequestRequest;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use Illuminate\Http\JsonResponse;

class RequestController extends BaseApiController
{
    private $requestService;
    private $lookupService;

    public function __construct(RequestServiceInterface $requestService,
                                LookupServiceInterface $lookupService,
                                NotificationServiceInterface $notificationService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('responseRejectRequest', 'deleteRequestRoom');

        $this->requestService = $requestService;
        $this->lookupService = $lookupService;
        $this->notificationService = $notificationService;
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
        $this->notificationService->proposalToRejectedOrResponseRequestRoomNotification($requestModel);
        return $this->noContentResponse();
    }
}
