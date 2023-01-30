<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestEmailDTO;
use App\Models\Package;
use App\Models\Request;
use App\Models\RequestEmail;

interface RequestEmailServiceInterface extends BaseServiceInterface
{
    public function create(RequestEmailDTO $dto): RequestEmail;

    public function update(int $id, RequestEmailDTO $dto): RequestEmail;

    public function sendApprovedRequestRoomMail(Request $request): void;

    public function sendCancelledRequestRoomMail(Request $request): void;

    public function sendApprovedRequestDriverMail(Request $request): void;

    public function sendCancelledRequestDriverMail(Request $request): void;

    public function sendApprovedRequestCarMail(Request $request): void;

    public function sendCancelledRequestCarMail(Request $request): void;
}