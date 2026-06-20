<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Store the intended URL for after login
        if ($request->route() && !$request->routeIs('login')) {
            session(['url.intended' => $request->fullUrl()]);
        }

        // Always redirect to login, never to /home
        return route('login');
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            abort(response()->json([
                'message' => 'Your session has expired. Please log in again.',
                'redirect' => route('login')
            ], 401));
        }

        // Set flash message for session expiration
        session()->flash('warning', 'Your session has expired. Please log in again to continue.');
        
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }
}