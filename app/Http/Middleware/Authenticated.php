<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class Authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!superAdmin() && true === (new User())->hasPermission($request->route()->getName())) {
            abort(403);
        }
        return $next($request);
    }
}
