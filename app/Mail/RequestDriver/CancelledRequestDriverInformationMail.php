<?php

namespace App\Mail\RequestDriver;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancelledRequestDriverInformationMail extends Mailable
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
        $status = strtoupper($this->request->status->value);
        $code = $this->request->code;
        $startDate = $this->request->start_date;
        $endDate = $this->request->end_date;
        $car = $this->request->requestDriver->driverRequestSchedule->carSchedule->car;
        $carInformation = "$car->trademark $car->model Color $car->color, Placa $car->license_plate";
        $comment = $this->request->cancelRequest->cancel_comment;
        return $this
            ->to($this->email)
            ->subject("Movimiento solicitud de chofer $code a $status")
            ->markdown('mail.request-driver.cancelled-request-driver-information',[
                'status' => $status,
                'code' => $code,
                'startDate' => $startDate->format('d-m-Y'),
                'startTime' => $startDate->format('g:i A'),
                'endDate' => $endDate->format('d-m-Y'),
                'endTime' => $endDate->format('g:i A'),
                'car' => $carInformation,
                'comment' => $comment
        ]);
    }
}
