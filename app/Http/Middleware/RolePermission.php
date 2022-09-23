<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class RolePermission
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param ...$roles
     * @return AuthorizationException|mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $hasPermission = false;
        foreach ($roles as $rol) {
            if (auth()->user()->role->name === $rol) {
                $hasPermission = true;
                break;
            }
        }

        return ($hasPermission) ? $next($request) : new AuthorizationException();
    }
}
