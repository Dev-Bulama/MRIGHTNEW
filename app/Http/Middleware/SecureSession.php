<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecureSession
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated but session is invalid
        if (Auth::check() && !$request->session()->has('_token')) {
            Auth::logout();
            $request->session()->invalidate();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired'], 419);
            }
            
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }
        
        $response = $next($request);
        
        // Add no-cache headers for authenticated routes
        if (Auth::check()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
        
        return $response;
    }
}