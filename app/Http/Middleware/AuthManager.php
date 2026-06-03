<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role_id != 6) {
            return redirect('logout');
        }

        return $next($request);
    }
}
