<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'current_password' => ['nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'phone_number.required' => 'Phone number is required.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    /**
 * Disable the user's account instead of deleting it.
 */
public function destroy(Request $request): RedirectResponse
{
    $request->validate([
        'password' => ['required', 'current_password'],
        'deletion_reason' => ['nullable', 'string', 'max:500'],
    ], [
        'password.required' => 'Password is required to disable account.',
        'password.current_password' => 'The password is incorrect.',
    ]);

    $user = $request->user();

    try {
        // Instead of deleting, we disable the account
        $user->update([
            'status' => 'inactive',
            'deleted_at' => now(),
            'deletion_reason' => $request->deletion_reason ?? 'User requested account deletion',
        ]);

        // Log the account disabling for audit purposes
        \Log::info('User account disabled', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'deletion_reason' => $request->deletion_reason ?? 'User requested account deletion',
            'disabled_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Logout the user
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Your account has been disabled successfully. Your data is preserved for record-keeping purposes.');
        
    } catch (\Exception $e) {
        \Log::error('Failed to disable user account: ' . $e->getMessage());
        
        return back()->with('error', 'Failed to disable account. Please try again or contact support.');
    }
}
    // public function destroy(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'password' => ['required', 'current_password'],
    //     ], [
    //         'password.required' => 'Password is required to delete account.',
    //         'password.current_password' => 'The password is incorrect.',
    //     ]);

    //     $user = $request->user();

    //     Auth::logout();

    //     $user->delete();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return Redirect::to('/')->with('success', 'Your account has been deleted successfully.');
    // }
}