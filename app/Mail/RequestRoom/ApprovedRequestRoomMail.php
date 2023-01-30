<?php

namespace App\Mail\RequestRoom;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedRequestRoomMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emails;
    protected $request;

    public function __construct(array $emails, Request $request)
    {
        $this->emails = $emails;
        $this->request = $request;
    }

    public function build(): ApprovedRequestRoomMail
    {
        $status = strtoupper($this->request->status->name);
        $code = $this->request->code;
        return $this
            ->to($this->emails)
            ->subject("Movimiento solicitud de sala $code a $status")
            ->markdown('mail.request-room.approved-request-room', [
                'code' => $code,
                'status' => $status,
                'date' => $this->request->start_date->format('d-m-Y'),
                'startTime' => $this->request->start_date->format('g:i A'),
                'endTime' => $this->request->end_date->format('g:i A'),
                'office' => $this->request->requestRoom->room->office->name,
                'room' => $this->request->requestRoom->room->name
            ]);
    }
}
