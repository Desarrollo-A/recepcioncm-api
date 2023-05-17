<?php

namespace App\Mail\RequestPackage;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedRequestPackageInformationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $requestPackage;
    public $email;

    public function __construct(Package $requestPackage, string $email)
    {
        $this->requestPackage = $requestPackage;
        $this->email = $email;
    }

    public function build(): ApprovedRequestPackageInformationMail
    {
        $status = strtoupper($this->requestPackage->request->status->value);
        $code = $this->requestPackage->request->code;
        $arrivalState = $this->requestPackage->arrivalAddress->state;
        $pickupState = $this->requestPackage->arrivalAddress->state;
        $date = $this->requestPackage->request->start_date;
        $car = $this->requestPackage->driverPackageSchedule->carSchedule->car;
        $carInformation = "$car->trademark $car->model Color $car->color, Placa $car->license_plate";
        return $this
            ->to($this->email)
            ->subject("Movimiento solicitud de paquetería $code a $status")
            ->markdown('mail.request-package.approved-request-package-information',[
                'status' => $status,
                'code' => $code,
                'arrivalState' => $arrivalState,
                'pickupState' => $pickupState,
                'date' => $date->format('d-m-Y'),
                'car' => $carInformation
            ]);
    }
}
