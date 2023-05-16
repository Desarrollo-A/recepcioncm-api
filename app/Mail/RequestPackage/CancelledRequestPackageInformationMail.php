<?php

namespace App\Mail\RequestPackage;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancelledRequestPackageInformationMail extends Mailable
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
        $date = $this->request->start_date;
        $car = $this->request->package->driverPackageSchedule->carSchedule->car;
        $carInformation = "$car->trademark $car->model Color $car->color, Placa $car->license_plate";
        $comment = $this->request->cancelRequest->cancel_comment;
        return $this
            ->to($this->email)
            ->subject("Movimiento solicitud de paquetería $code a $status" )
            ->markdown('mail.request-package.cancelled-request-package-information',[
                'status' => $status,
                'code' => $code,
                'date' => $date->format('d-m-Y'),
                'car' => $carInformation,
                'comment' => $comment
            ]);
    }
}
