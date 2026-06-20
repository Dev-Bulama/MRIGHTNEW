<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    /**
 * The user has been authenticated.
 */
protected function authenticated(Request $request, $user)
{
    // Regenerate session ID after successful login
    $request->session()->regenerate();
    return redirect()->intended($this->redirectPath());
}

/**
 * Log the user out and add security headers.
 */
public function logout(Request $request)
{
    // Clear all session data
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    // Perform logout
    $this->guard()->logout();
    
    $response = redirect()->route('login')->with('status', 'You have been logged out successfully.');
    
    // Add security headers to prevent caching
    return $response->withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
}
}
