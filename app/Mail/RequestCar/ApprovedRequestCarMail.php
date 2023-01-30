<?php

namespace App\Mail\RequestCar;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedRequestCarMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emails;
    protected $request;

    public function __construct(array $emails, Request $request)
    {
        $this->emails = $emails;
        $this->request = $request;
    }

    public function build(): ApprovedRequestCarMail
    {
        $status = strtoupper($this->request->status->name);
        $code = $this->request->code;
        $car = $this->request->requestCar->carRequestSchedule->carSchedule->car;
        $carInformation = "$car->trademark $car->model Color $car->color, Placa $car->license_plate";

        return $this
            ->to($this->emails)
            ->subject("Movimiento solicitud de vehículo $code a $status")
            ->markdown('mail.request-car.approved-request-car', [
                'code' => $code,
                'status' => $status,
                'startDate' => $this->request->start_date->format('d-m-Y'),
                'endDate' => $this->request->end_date->format('d-m-Y'),
                'startTime' => $this->request->start_date->format('g:i A'),
                'endTime' => $this->request->end_date->format('g:i A'),
                'car' => $carInformation
            ]);
    }
}
