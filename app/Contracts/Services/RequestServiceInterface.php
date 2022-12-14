<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestDTO;
use App\Models\Package;
use App\Models\Request;
use App\Models\RequestDriver;
use Illuminate\Database\Eloquent\Collection;

interface RequestServiceInterface extends BaseServiceInterface
{
    public function deleteRequestRoom(int $requestId, int $userId): Request;

    public function responseRejectRequest(int $id, RequestDTO $dto): Request;

    /**
     * @return void
     */
    public function updateCode(Request $request);

    public function changeToFinished(): Collection;

    /**
     * @return void
     */
    public function changeToExpired();

    public function deleteRequestPackage(int $requestId): Package;

    public function deleteRequestDriver(int $requestId): RequestDriver;
}