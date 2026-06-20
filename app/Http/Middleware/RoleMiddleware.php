<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }

            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}

/**
 * Middleware for checking multiple permissions (ANY)
 */
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: permission:users.view,shops.view (user needs ANY of these permissions)
     */
    public function handle(Request $request, Closure $next, string $permissions)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $permissionArray = explode(',', $permissions);

        if (!$user->hasAnyPermission($permissionArray)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }

            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}

/**
 * Middleware for checking multiple permissions (ALL)
 */
class RequireAllPermissionsMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: require_all:users.view,users.edit (user needs ALL of these permissions)
     */
    public function handle(Request $request, Closure $next, string $permissions)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $permissionArray = explode(',', $permissions);

        if (!$user->hasAllPermissions($permissionArray)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have sufficient permissions to perform this action.'
                ], 403);
            }

            abort(403, 'You do not have sufficient permissions to access this page.');
        }

        return $next($request);
    }
}

/**
 * Middleware for checking user types
 */
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

/**
 * Middleware for union location-based access
 */
class UnionLocationMiddleware
{
    /**
     * Handle an incoming request.
     * Checks if union user can access resources in specific locations
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Only apply to union users
        if (!$user->isUnion()) {
            return $next($request);
        }

        // Get state and LGA from request (shop management, etc.)
        $state = $request->input('state') ?? $request->route('state');
        $lga = $request->input('lga') ?? $request->route('lga');

        // If accessing shop-specific resources, check the shop's location
        if ($request->route('shop')) {
            $shop = $request->route('shop');
            $state = $shop->state;
            $lga = $shop->local_government;
        }

        // Check if union can manage this location
        if ($state && !$user->canManageShopInLocation($state, $lga)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to manage resources in this location.'
                ], 403);
            }

            abort(403, 'You are not authorized to access resources in this location.');
        }

        return $next($request);
    }
}