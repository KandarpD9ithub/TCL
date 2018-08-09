<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BasicAuth
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
        $email =  Request::header('PHP_AUTH_USER');
        $password =  Request::header('PHP_AUTH_PW');

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            if (\Auth::User()->role_name == '1') {
                return response()->error('Bad Request', 401);
            }
            return $next($request);
        }
        return response()->error('unauthorized', 401);

    }
}
