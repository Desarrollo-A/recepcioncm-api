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

    /**
     * @return void
     */
    public function updateCode(Request $request);

    public function changeToFinished(): Collection;

    public function changeToExpired(): void;

    public function deleteRequestPackage(int $requestId, int $userId): Package;

    public function deleteRequestDriver(int $requestId, int $userId): RequestDriver;
}