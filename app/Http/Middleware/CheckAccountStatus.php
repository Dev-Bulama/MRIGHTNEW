<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user account is inactive or deleted
            if ($user->status === 'inactive' || $user->deleted_at !== null) {
                Auth::logout();
                
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Your account has been disabled. Please contact support if you believe this is an error.');
            }
            
            // Check if user is suspended
            if ($user->status === 'suspended') {
                Auth::logout();
                
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Your account has been suspended. Please contact support.');
            }
        }
        
        return $next($request);
    }
}