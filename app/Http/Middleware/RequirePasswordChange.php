<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && 
            auth()->user()->password_changed_at === null && 
            !$request->routeIs('password.change') && 
            !$request->routeIs('password.update') &&
            !$request->routeIs('logout')) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
