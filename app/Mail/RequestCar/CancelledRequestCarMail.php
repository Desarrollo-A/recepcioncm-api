<?php

namespace App\Mail\RequestCar;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancelledRequestCarMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emails;
    protected $request;

    public function __construct(array $emails, Request $request)
    {
        $this->emails = $emails;
        $this->request = $request;
    }

    public function build(): CancelledRequestCarMail
    {
        $status = strtoupper($this->request->status->value);
        $code = $this->request->code;

        return $this
            ->to($this->emails)
            ->subject("Movimiento solicitud de chofer $code a $status")
            ->markdown('mail.request-car.cancelled-request-car', [
                'code' => $code,
                'status' => $status,
                'startDate' => $this->request->start_date->format('d-m-Y'),
                'endDate' => $this->request->end_date->format('d-m-Y'),
                'startTime' => $this->request->start_date->format('g:i A'),
                'endTime' => $this->request->end_date->format('g:i A'),
                'comment' => $this->request->cancelRequest->cancel_comment
            ]);
    }
}
