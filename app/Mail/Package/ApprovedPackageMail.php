<?php

namespace App\Mail\Package;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovedPackageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $packageUpdated;
    public $codeRequest;
    
    public function __construct(Package $packageUpdated, string $codeRequest)
    {
        $this->packageUpdated   =   $packageUpdated;
        $this->codeRequest      =   $codeRequest;
    }

    public function build(): ApprovedPackageMail
    {   
        return $this
            ->subject('Calificacion para solicitud de paqueteria')
            ->markdown('mail.package.approved-package',[
                'fullName'          =>  $this->packageUpdated->name_receive,
                'codeRequest'       =>  $this->codeRequest,
                'codeAuthRequest'   =>  $this->packageUpdated->auth_code,
                'url'               =>  'http://localhost:4200/#/paqueteria/'.$this->packageUpdated->request_id.'?code='.$this->packageUpdated->auth_code
            ]);
    }
}
