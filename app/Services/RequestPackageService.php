<?php

namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\RequestPackageServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Models\Dto\PackageDTO;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Package;

class RequestPackageService extends BaseService implements RequestPackageServiceInterface
{
    protected $packageRepository;
    protected $requestRepository;
    protected $lookupRepository;
    protected $addressRepository;

    public function __construct(RequestRepositoryInterface $requestRepository,
                                PackageRepositoryInterface $packageRepository,
                                LookupRepositoryInterface $lookupRepository,
                                AddressRepositoryInterface $addressRepository)
    {
        $this->requestRepository = $requestRepository;
        $this->packageRepository = $packageRepository;
        $this->lookupRepository = $lookupRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestPackage(PackageDTO $dto): Package
    {
        $pickupAddress = $this->addressRepository->create($dto->pickupAddress->toArray(['street', 'num_ext', 'num_int',
            'suburb', 'postal_code', 'state', 'country_id']));
        $dto->pickup_address_id = $pickupAddress->id;

        $arrivalAddress = $this->addressRepository->create($dto->arrivalAddress->toArray(['street', 'num_ext', 'num_int',
            'suburb', 'postal_code', 'state', 'country_id']));
        $dto->arrival_address_id = $arrivalAddress->id;

        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
                TypeLookup::STATUS_ROOM_REQUEST)
            ->id;

        $dto->request->type_id = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::PARCEL),
            TypeLookup::TYPE_REQUEST)
            ->id;

        $request = $this->requestRepository->create($dto->request->toArray(['title', 'start_date', 'comment', 'type_id',
            'add_google_calendar', 'user_id', 'status_id']));
        $dto->request_id = $request->id;

        $package = $this->packageRepository->create($dto->toArray(['pickup_address_id', 'arrival_address_id',
            'name_receive', 'email_receive', 'comment_receive', 'request_id', 'office_id']));
        return $package->fresh(['request', 'pickupAddress', 'arrivalAddress']);
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $id, PackageDTO $dto): void
    {
        $dto->authorization_filename = File::uploadFile($dto->authorization_file, Path::PACKAGE_AUTHORIZATION_DOCUMENTS);
        $this->packageRepository->update($id, $dto->toArray(['authorization_filename']));
    }
}