<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CraftsmanMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isCraftsman()) {
            return redirect()->route('auth.login');
        }
        return $next($request);
    }
}
