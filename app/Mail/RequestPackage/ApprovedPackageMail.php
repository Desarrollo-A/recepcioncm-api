<?php

namespace App\Mail\RequestPackage;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedPackageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $package;
    public $codeRequest;
    
    public function __construct(Package $package, string $codeRequest)
    {
        $this->package      =   $package;
        $this->codeRequest  =   $codeRequest;
    }

    public function build(): ApprovedPackageMail
    {   
        $url = config('app.url_front').'paqueteria/'.$this->package->request_id.'?code='.$this->package->auth_code;
        return $this
            ->subject('Solicitud de paquetería '.$this->codeRequest)
            ->markdown('mail.request-package.score-request-package', [
                'fullName'      =>  $this->package->name_receive,
                'codeRequest'   =>  $this->codeRequest,
                'url'           =>  $url
            ]);
    }
}
