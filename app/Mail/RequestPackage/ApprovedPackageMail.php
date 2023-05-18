<?php

namespace App\Mail\RequestPackage;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedPackageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Package
     */
    public $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    public function build(): ApprovedPackageMail
    {
        $code = $this->package->request->code;
        $endDate = $this->package->request->end_date->format('d-m-Y');

        return $this
            ->to($this->package->email_receive)
            ->subject("Solicitud $code aprobada")
            ->markdown('mail.request-package.approved-package', [
                'code' => $code,
                'deliveryDate' => $endDate
            ]);
    }
}
