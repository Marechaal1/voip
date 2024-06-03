<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $username = env('BASIC_AUTH_USERNAME');
        $password = env('BASIC_AUTH_PASSWORD');

        if (!$request->header('PHP_AUTH_USER') || !$request->header('PHP_AUTH_PW') ||
            $request->header('PHP_AUTH_USER') !== $username || $request->header('PHP_AUTH_PW') !== $password) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
