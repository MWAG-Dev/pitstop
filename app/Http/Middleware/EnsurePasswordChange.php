<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePasswordChange
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        if (
            $request->routeIs('profile.edit')
            || $request->routeIs('profile.update')
            || $request->routeIs('password.update')
            || $request->routeIs('logout')
        ) {
            return $next($request);
        }

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Please change your temporary password before continuing.');
    }
}
