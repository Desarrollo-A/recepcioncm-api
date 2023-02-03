<?php

namespace App\Mail\RequestDriver;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedRequestDriverInformationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $email;
    
    public function __construct(Request $request, string $email)
    {
        $this->request = $request;
        $this->email = $email;
    }

    public function build()
    {
        $status = strtoupper($this->request->status->name);
        $code = $this->request->code;
        $arrivalState = $this->request->requestDriver->arrivalAddress->state;
        $pickupState = $this->request->requestDriver->arrivalAddress->state;
        $startDate = $this->request->start_date;
        $endDate = $this->request->end_date;
        $car = $this->request->requestDriver->driverRequestSchedule->carSchedule->car;
        $carInformation = "$car->trademark $car->model Color $car->color, Placa $car->license_plate";
        return $this
            ->to($this->email)
            ->subject("Movimiento solicitud de chofer $code a $status")
            ->markdown('mail.request-driver.approved-request-driver-information',[
                'status' => $status,
                'code' => $code,
                'arrivalState' => $arrivalState,
                'pickupState' => $pickupState,
                'startDate' => $startDate->format('d-m-Y'),
                'startTime' => $startDate->format('g:i A'),
                'endDate' => $endDate->format('d-m-Y'),
                'endTime' => $endDate->format('g:i A'),
                'car' => $carInformation
            ]);
    }
}
