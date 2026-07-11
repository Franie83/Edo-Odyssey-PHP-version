<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('auth.login')->with('danger', 'Please log in.');
        }

        $adminRoles = ['Agency Admin', 'Super Admin'];
        if (!in_array($request->user()->role, $adminRoles)) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
