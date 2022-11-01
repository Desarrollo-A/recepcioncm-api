<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestEmailServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\RequestEmail\StoreEmailRequest;
use App\Http\Requests\RequestEmail\UpdateEmailRequest;
use App\Http\Resources\RequestEmail\EmailResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class RequestEmailController extends BaseApiController
{
    private $requestEmailService;

    public function __construct(RequestEmailServiceInterface $requestEmailService)
    {
        $this->middleware('role.permission:' . NameRole::APPLICANT);
        $this->requestEmailService = $requestEmailService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreEmailRequest $request): JsonResponse
    {
        $email = $this->requestEmailService->create($request->toDTO());
        return $this->showOne(new EmailResource($email));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, UpdateEmailRequest $request): JsonResponse
    {
        $email = $this->requestEmailService->update($id, $request->toDTO());
        return $this->showOne(new EmailResource($email));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->requestEmailService->delete($id);
        return $this->noContentResponse();
    }
}
