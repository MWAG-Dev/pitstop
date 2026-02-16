<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        $user = auth()->user();

        abort_if(! $user || ! $user->isAdmin(), 403);

        return $next($request);
    }
}
