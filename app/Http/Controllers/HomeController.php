<?php

namespace App\Http\Controllers;

use App\Core\BaseApiController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->successResponse(['message' => 'Server works'], Response::HTTP_OK);
    }
}