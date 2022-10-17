<?php

namespace App\Console\Commands\Request;

use App\Contracts\Services\RequestServiceInterface;
use Illuminate\Console\Command;

class ExpiredRequest extends Command
{
    private $requestService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pasar todas las solicitudes sin atender a Expiradas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RequestServiceInterface $requestService)
    {
        parent::__construct();
        $this->requestService = $requestService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->requestService->changeToExpired();
    }
}
