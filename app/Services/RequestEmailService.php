<?php

namespace App\Services;

use App\Contracts\Repositories\RequestEmailRepositoryInterface;
use App\Contracts\Services\RequestEmailServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Mail\Request\ApprovedRequestMail;
use App\Mail\Request\CancelledRequestMail;
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

    public function sendApprovedRequestMail(Request $request)
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new ApprovedRequestMail($emails->toArray(), $request));
        }
    }

    public function sendCancelledRequestMail(Request $request)
    {
        $emails = $this->entityRepository->findByRequestId($request->id, ['email'])->pluck('email');
        if ($emails->count() > 0) {
            Mail::send(new CancelledRequestMail($emails->toArray(), $request));
        }
    }
}