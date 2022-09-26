<?php

namespace App\Mail\Auth;

use App\Helpers\Enum\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestorePasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newPassword;

    public function __construct(User $user, string $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function build(): RestorePasswordMail
    {
        return $this
            ->subject(Message::RESTORE_PASSWORD)
            ->markdown('mail.auth.restore-password', [
                'fullName' => $this->user->full_name,
                'newPassword' => $this->newPassword
            ]);
    }
}
