<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Illuminate\Http\UploadedFile;

class PackageDTO
{
    use DataTransferObject;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var int
     */
    public $pickup_address_id;

    /**
     * @var AddressDTO
     */
    public $pickupAddress;

    /**
     * @var int
     */
    public $arrival_address_id;

    /**
     * @var AddressDTO
     */
    public $arrivalAddress;

    /**
     * @var string
     */
    public $name_receive;

    /**
     * @var string
     */
    public $email_receive;

    /**
     * @var string
     */
    public $comment_receive;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var integer
     */
    public $office_id;

    /**
     * @var RequestDTO
     */
    public $request;

    /**
     * @var DriverPackageScheduleDTO
     */
    public $driverPackageSchedule;

    /**
     * @var string
     */
    public $auth_code;

    /**
     * @var boolean
     */
    public $is_urgent;

    /**
     * @var bool
     */
    public $is_heavy_shipping;

    /**
     * @var HeavyShipmentDTO[]
     */
    public $heavyShipments;

    /**
     * @var ProposalPackageDTO
     */
    public $proposalPackage;

    /**
     * @var ProposalRequestDTO
     */
    public $proposalRequest;

    /**
     * @var DetailExternalParcelDTO
     */
    public $detailExternalParcel;

    /**
     * @throws CustomErrorException
     */
    public function __construct(array $data = [])
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }
}