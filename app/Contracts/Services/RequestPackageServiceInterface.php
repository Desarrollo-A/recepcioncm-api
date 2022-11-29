<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\ScoreDTO;
use App\Models\Package;

interface RequestPackageServiceInterface extends BaseServiceInterface
{
    public function createRequestPackage(PackageDTO $dto): Package;

    public function uploadAuthorizationFile(int $id, PackageDTO $dto): void;

    public function insertScore(ScoreDTO $score): void;

    public function isPackageCompleted(int $requestPackageId): bool;
}