<?php

namespace App\Core;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BaseApiController extends Controller
{
    use ApiResponse, AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}