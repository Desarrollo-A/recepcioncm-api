<?php

namespace App\Observers;

use App\Contracts\Services\RequestServiceInterface;
use App\Models\Request;

class RequestObserver
{
    private $requestService;

    public function __construct(RequestServiceInterface $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Request $request)
    {
        $this->requestService->updateCode($request);
    }
}