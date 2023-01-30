<?php

namespace App\Services;

use App\Contracts\Repositories\RequestEmailRepositoryInterface;
use App\Contracts\Services\RequestEmailServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Mail\RequestCar\ApprovedRequestCarMail;
use App\Mail\RequestCar\CancelledRequestCarMail;
use App\Mail\RequestDriver\ApprovedRequestDriverMail;
use App\Mail\RequestDriver\CancelledRequestDriverMail;
use App\Mail\RequestRoom\ApprovedRequestRoomMail;
use App\Mail\RequestRoom\CancelledRequestRoomMail;
use App\Models\Dto\RequestEmailDTO;
use App\Models\Request;
use App\Models\RequestEmail;
use Illuminate\Support\Facades\Mail;

class RequestEmailService extends BaseService implements RequestEmailServiceInterface
{
    protected $entityRepository;

    public function __construct(RequestEmailRepositoryInterface $requestEmailRepository)
    {
        $this->entityRepository = $requestEmailRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RequestEmailDTO $dto): RequestEmail
    {
        return $this->entityRepository->create($dto->toArray(['name', 'email', 'request_id']));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, RequestEmailDTO $dto): RequestEmail
    {
        return $this->entityRepository->update($id, $dto->toArray(['name', 'email', 'request_id']));
    }

    public function sendApprovedRequestRoomMail(Request $request): void
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new ApprovedRequestRoomMail($emails->toArray(), $request));
        }
    }

    public function sendCancelledRequestRoomMail(Request $request): void
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new CancelledRequestRoomMail($emails->toArray(), $request));
        }
    }

    public function sendApprovedRequestDriverMail(Request $request): void
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new ApprovedRequestDriverMail($emails->toArray(), $request));
        }
    }

    public function sendCancelledRequestDriverMail(Request $request): void
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new CancelledRequestDriverMail($emails->toArray(), $request));
        }
    }

    public function sendApprovedRequestCarMail(Request $request): void
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new ApprovedRequestCarMail($emails->toArray(), $request));
        }
    }

    public function sendCancelledRequestCarMail(Request $request): void
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new CancelledRequestCarMail($emails->toArray(), $request));
        }
    }
}