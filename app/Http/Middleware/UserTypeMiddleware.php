<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: user_type:admin,union (user must be admin OR union)
     */
    public function handle(Request $request, Closure $next, string $userTypes)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $allowedTypes = explode(',', $userTypes);

        if (!in_array($user->user_type, $allowedTypes)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account type does not have access to this resource.'
                ], 403);
            }

            abort(403, 'Your account type does not have access to this page.');
        }

        return $next($request);
    }
}