<?php

namespace App\Mail\RequestPackage;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelledPackageMail extends Mailable
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

    public function build(): CancelledPackageMail
    {
        return $this
            ->to($this->email)
            ->subject("Solicitud $this->code cancelada")
            ->markdown('mail.request-package.cancelled-package', [
                'code' => $this->code
            ]);
    }
}
