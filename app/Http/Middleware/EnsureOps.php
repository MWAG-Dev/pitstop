<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class EnsureOps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        $user = auth()->user();

        abort_if(! $user || ! $user->isOps(), 403);

        return $next($request);
    }
}
