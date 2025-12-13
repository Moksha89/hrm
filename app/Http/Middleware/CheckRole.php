<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!auth()->check()) {
            \Log::warning('CheckRole: User not authenticated', [
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
            ]);
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if (auth()->user()->hasRole($role)) {
                return $next($request);
            }
        }

        \Log::warning('CheckRole: User lacks required role', [
            'user_id' => auth()->id(),
            'user_roles' => auth()->user()->roles->pluck('name')->toArray(),
            'required_roles' => $roles,
            'route' => $request->route()?->getName(),
        ]);

        abort(403, 'Unauthorized access.');
    }
}
