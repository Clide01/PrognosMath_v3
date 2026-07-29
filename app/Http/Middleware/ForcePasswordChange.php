<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->force_password_change) {
            // Allow them to visit the setup page or logout, block everything else
            if (!$request->routeIs('password.setup') && !$request->routeIs('password.setup.store') && !$request->routeIs('logout')) {
                return redirect()->route('password.setup');
            }
        }

        return $next($request);
    }
}