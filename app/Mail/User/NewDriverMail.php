<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewDriverMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function build(): NewDriverMail
    {
        return $this
            ->subject('Usuario registrado')
            ->markdown('mail.user.new-driver', [
                'fullName' => $this->user->full_name,
                'password' => $this->password,
                'urlFront' => config('app.url_front'),
                'code' => $this->user->no_employee
            ]);
    }
}
