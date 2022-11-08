<?php

namespace App\Console\Commands\Request;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use Illuminate\Console\Command;

class FinishedRequest extends Command
{
    private $requestService;
    private $notificationService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request:finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cambiar estatus de solicitudes Aprobadas a Terminadas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RequestServiceInterface $requestService,
                                NotificationServiceInterface $notificationService)
    {
        parent::__construct();
        $this->requestService = $requestService;
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $requests = $this->requestService->changeToFinished();
        if ($requests->count() > 0) {
            $this->notificationService->createScoreRequestNotification($requests);
        }
    }
}
