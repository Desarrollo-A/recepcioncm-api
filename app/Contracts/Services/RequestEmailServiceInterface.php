<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestEmailDTO;
use App\Models\Request;
use App\Models\RequestEmail;

interface RequestEmailServiceInterface extends BaseServiceInterface
{
    public function create(RequestEmailDTO $dto): RequestEmail;

    public function update(int $id, RequestEmailDTO $dto): RequestEmail;

    /**
     * @return void
     */
    public function sendMailNotification(Request $request);
}