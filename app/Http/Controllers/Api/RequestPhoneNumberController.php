<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestPhoneNumberServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\RequestPhoneNumber\StorePhoneNumberRequest;
use App\Http\Requests\RequestPhoneNumber\UpdatePhoneNumberRequest;
use App\Http\Resources\RequestPhoneNumber\PhoneNumberResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class RequestPhoneNumberController extends BaseApiController
{
    private $requestPhoneNumberService;

    public function __construct(RequestPhoneNumberServiceInterface $requestPhoneNumberService)
    {
        $this->middleware('role.permission:' . NameRole::APPLICANT);
        $this->requestPhoneNumberService = $requestPhoneNumberService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StorePhoneNumberRequest $request): JsonResponse
    {
        $phone = $this->requestPhoneNumberService->create($request->toDTO());
        return $this->showOne(new PhoneNumberResource($phone));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, UpdatePhoneNumberRequest $request): JsonResponse
    {
        $phone = $this->requestPhoneNumberService->update($id, $request->toDTO());
        return $this->showOne(new PhoneNumberResource($phone));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->requestPhoneNumberService->delete($id);
        return $this->noContentResponse();
    }
}
