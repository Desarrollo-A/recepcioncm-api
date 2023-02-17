<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CheckCustomToken
{
    /**
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next, string $configToken)
    {
        $token = $request->bearerToken();
        if ($token !== config($configToken, 'XXX')) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
