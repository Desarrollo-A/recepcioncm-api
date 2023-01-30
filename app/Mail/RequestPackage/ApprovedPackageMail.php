<?php

namespace App\Mail\RequestPackage;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
        $url = config('app.url_front').'paqueteria/'.$this->packageUpdated->request_id.'?code='.$this->packageUpdated->auth_code;
        return $this
            ->subject('Solicitud de paquetería '.$this->codeRequest)
            ->markdown('mail.request-package.approved-package',[
                'fullName'          =>  $this->packageUpdated->name_receive,
                'codeRequest'       =>  $this->codeRequest,
                'url'               =>  $url
            ]);
    }
}
