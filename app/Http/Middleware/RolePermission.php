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
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $hasPermission = false;
        $roleName = auth()->user()->role->name;
        foreach ($roles as $rol) {
            if ($roleName === $rol) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
