<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isClient()) {
            return redirect()->route('auth.login');
        }
        return $next($request);
    }
}
