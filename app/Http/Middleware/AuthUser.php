<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InSession;
use Symfony\Component\HttpFoundation\Response;

class AuthUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guest()) {
            if ($request->ajax()) {
                return response('Unauthorized', 401);
            }
            return redirect('login');
        }

        if (Auth::user()->office_id > 0) {
            InSession::record();
        }

        return $next($request);
    }
}
