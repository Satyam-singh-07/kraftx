<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email to continue.',
                'login_required' => true,
            ], 401);
        }

        return redirect()
            ->guest(route('home'))
            ->with([
                'auth_modal' => 'sign',
                'auth_notice' => 'Please verify your email to continue.',
            ]);
    }
}
