<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestDTO;
use App\Models\Request;

interface RequestServiceInterface extends BaseServiceInterface
{
    public function deleteRequestRoom(int $requestId, int $userId): Request;

    public function responseRejectRequest(int $id, RequestDTO $dto): Request;

    /**
     * @return void
     */
    public function updateCode(Request $request);

    /**
     * @return void
     */
    public function changeToFinished();

    /**
     * @return void
     */
    public function changeToExpired();
}