<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowed): Response
    {
        $reqRole = $request->role ?? "";
        foreach ($allowed as $role) {
            if ($reqRole === $role) {
                return $next($request);
            }
        }
        abort(403);
    }
}
