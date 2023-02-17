<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CheckCronJobToken
{
    /**
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if ($token !== config('app.token_cron_jobs', 'XXX')) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
