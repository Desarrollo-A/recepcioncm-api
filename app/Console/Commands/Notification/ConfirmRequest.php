<?php

namespace App\Console\Commands\Notification;

use App\Contracts\Services\NotificationServiceInterface;
use Illuminate\Console\Command;

class ConfirmRequest extends Command
{
    private $notificationService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:confirm-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear notificación de confirmación de solicitud';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationServiceInterface $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->notificationService->createConfirmNotification();
    }
}
