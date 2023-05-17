<?php

namespace App\Mail\RequestPackage;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnRoadPackageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $email;

    public function __construct(string $code, string $email)
    {
        $this->code = $code;
        $this->email = $email;
    }

    public function build(): OnRoadPackageMail
    {
        return $this
            ->to($this->email)
            ->subject("Solicitud $this->code en camino")
            ->markdown('mail.request-package.on-road-package', ['code' => $this->code]);
    }
}
