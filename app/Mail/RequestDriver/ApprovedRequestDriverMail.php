<?php

namespace App\Mail\RequestDriver;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedRequestDriverMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emails;
    protected $request;

    public function __construct(array $emails, Request $request)
    {
        $this->emails = $emails;
        $this->request = $request;
    }

    public function build(): ApprovedRequestDriverMail
    {
        $status = strtoupper($this->request->status->name);
        $code = $this->request->code;
        $car = $this->request->requestDriver->driverRequestSchedule->carSchedule->car;
        $carInformation = "$car->trademark $car->model Color $car->color, Placa $car->license_plate";

        return $this
            ->to($this->emails)
            ->subject("Movimiento solicitud de chofer $code a $status")
            ->markdown('mail.request-driver.approved-request-driver', [
                'code' => $code,
                'status' => $status,
                'startDate' => $this->request->start_date->format('d-m-Y'),
                'endDate' => $this->request->end_date->format('d-m-Y'),
                'startTime' => $this->request->start_date->format('g:i A'),
                'endTime' => $this->request->end_date->format('g:i A'),
                'driver' => $this->request->requestDriver->driverRequestSchedule->driverSchedule->driver->full_name,
                'car' => $carInformation
            ]);
    }
}
