<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PreApprovedUser;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
$request->validate([
    'full_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', 'min:3'],
    'email' => [
        'required', 
        'string', 
        'email', 
        'max:255', 
        Rule::unique('users', 'email')->whereNull('deleted_at')
    ],
    'phone_number' => [
        'required', 
        'string', 
        'max:20', 
        Rule::unique('users', 'phone_number')->whereNull('deleted_at')
    ],
    'secondary_phone' => [
        'nullable', 
        'string', 
        'max:20', 
        Rule::unique('users', 'secondary_phone')->whereNull('deleted_at'),
        Rule::unique('users', 'phone_number')->whereNull('deleted_at')
    ],
    'user_type' => ['required', 'in:customer,shop_owner,agent'],
    'password' => ['required', 'confirmed', Rules\Password::defaults()],
    'terms' => ['required', 'accepted'],
], [
    'full_name.required' => 'Full name is required.',
    'full_name.regex' => 'Full name can only contain letters and spaces.',
    'full_name.min' => 'Full name must be at least 3 characters.',
    'phone_number.unique' => 'This phone number is already registered.',
    'secondary_phone.unique' => 'This phone number is already registered as either primary or secondary phone.',
    'terms.required' => 'You must accept the terms of service.',
    'terms.accepted' => 'You must accept the terms of service.',
]);

        // Check for duplicate phone numbers across both fields
        $phoneExists = User::where(function($query) use ($request) {
            $query->where('phone_number', $request->phone_number)
                  ->orWhere('secondary_phone', $request->phone_number);
            
            if ($request->secondary_phone) {
                $query->orWhere('phone_number', $request->secondary_phone)
                      ->orWhere('secondary_phone', $request->secondary_phone);
            }
        })->exists();

        if ($phoneExists) {
            return back()->withInput()->withErrors([
                'phone_number' => 'One or both phone numbers are already registered.',
            ]);
        }

        // Clean phone numbers
// Clean phone numbers
        $primaryPhone = $this->cleanPhoneNumber($request->phone_number);
        $secondaryPhone = $request->secondary_phone ? $this->cleanPhoneNumber($request->secondary_phone) : null;

        // Check if user is pre-approved for auto-activation
      // FIXED: Check if user is pre-approved for auto-activation (BOTH admin and union)

// Check admin pre-approvals first
$adminPreApproved = PreApprovedUser::where('status', 'pending')
    ->where('user_type', $request->user_type)
    ->where(function($query) use ($request, $primaryPhone) {
        $query->where('email', $request->email)
              ->orWhere(function($q) use ($primaryPhone) {
                  $cleanPhone = preg_replace('/[^0-9+]/', '', $primaryPhone);
                  $q->where('phone_number', 'like', "%{$cleanPhone}%")
                    ->orWhere('phone_number', $primaryPhone);
              });
    })
    ->first();

// Check union pre-approvals (only for shop owners)
$unionPreApproved = null;
if ($request->user_type === 'shop_owner') {
    $unionPreApproved = \App\Models\PreApproval::where('status', 'pending')
        ->where(function($query) use ($request, $primaryPhone) {
            $query->where('email', $request->email)
                  ->orWhere(function($q) use ($primaryPhone) {
                      $cleanPhone = preg_replace('/[^0-9+]/', '', $primaryPhone);
                      $q->where('phone_number', 'like', "%{$cleanPhone}%")
                        ->orWhere('phone_number', $primaryPhone);
                  });
        })
        ->first();
}

// Use whichever pre-approval exists (admin takes priority)
$preApproved = $adminPreApproved ?: $unionPreApproved;
$approvalSource = $adminPreApproved ? 'admin' : 'union';

        // Set status based on pre-approval
        $userStatus = $preApproved ? User::STATUS_ACTIVE : User::STATUS_PENDING;

        // Create the user
    //     $user = User::create([
    //         'first_name' => $request->first_name,
    //         'last_name' => $request->last_name,
    //         'name' => $request->first_name . ' ' . $request->last_name,
    //         'email' => $request->email,
    //         'phone_number' => $primaryPhone,
    //         'secondary_phone' => $secondaryPhone,
    //         'user_type' => $request->user_type,
    //         'status' => $userStatus,
    //         'password' => Hash::make($request->password),
    //         'email_verified_at' => $preApproved ? now() : null, // Auto-verify if pre-approved
    //         'account_activated_at' => $preApproved ? now() : null,
    //     ]);

    //     // Mark pre-approval as used
    //     if ($preApproved) {
    //         $preApproved->markAsUsed($user->id);
    //     }

    //     event(new Registered($user));

    //     // Send welcome email (simple version without Mail class for now)
    //     try {
    //         // We'll implement this later when email is fully configured
    //         // For now, just log the success
    //         \Log::info('User registered successfully: ' . $user->email);
    //     } catch (\Exception $e) {
    //         \Log::error('Registration process error: ' . $e->getMessage());
    //     }

    //     // Redirect to login with appropriate message
    //     $message = $preApproved 
    //         ? 'Account created and activated successfully! You can now log in and start using the system.'
    //         : 'Account created successfully! Please log in with your credentials.';
            
    //     return redirect()->route('login')->with('success', $message);
    // }
// Create the user
        // Create the user
     // Split full name into first and last name
$nameParts = explode(' ', trim($request->full_name));
$firstName = $nameParts[0];
$lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

// Create the user
$user = User::create([
    'first_name' => $firstName,
    'last_name' => $lastName,
    'name' => $request->full_name,
    'email' => $request->email,
    'phone_number' => $primaryPhone,
    'secondary_phone' => $secondaryPhone,
    'user_type' => $request->user_type,
    'status' => $userStatus,
    'password' => Hash::make($request->password),
    'email_verified_at' => $preApproved ? now() : null,
    'account_activated_at' => $preApproved ? now() : null,
]);

        // Auto-create shop profile for pre-approved shop owners
        // if ($preApproved && $request->user_type === 'shop_owner' && $preApproved->shop_name) {
        //     $shop = \App\Models\Shop::create([
        //         'user_id' => $user->id,
        //         'shop_name' => $preApproved->shop_name,
        //         'owner_full_name' => $user->name,
        //         'business_address' => $preApproved->business_address ?? '',
        //         'business_phone_1' => $preApproved->business_phone ?? $user->phone_number,
        //         'business_phone_2' => $user->secondary_phone,
        //         'business_email' => $user->email,
        //         'state' => $preApproved->state ?? '',
        //         'local_government' => $preApproved->local_government ?? '',
        //         'approved' => true,
        //         'approved_at' => now(),
        //         'approved_by' => 1,
        //         'status' => 'active',
        //         'registration_source' => 'pre_approved',
        //     ]);
            
        //     $user->update(['shop_id' => $shop->id]);
        // }
// Auto-create shop profile for pre-approved shop owners
       // Auto-create shop profile for pre-approved shop owners
// Auto-create shop profile for pre-approved shop owners
// if ($preApproved && $request->user_type === 'shop_owner' && $preApproved->shop_name) {
//     // Find the first available admin user for approved_by
//     $adminUser = User::where('user_type', User::TYPE_ADMIN)->first();
//     $approvedBy = $adminUser ? $adminUser->id : null;
    
//     $shop = \App\Models\Shop::create([
//         'user_id' => $user->id,
//         'shop_name' => $preApproved->shop_name,
//         'owner_full_name' => $user->name,
//         'business_address' => $preApproved->business_address ?? '',
//         'business_phone_1' => $preApproved->business_phone ?? $user->phone_number,
//         'business_phone_2' => $user->secondary_phone,
//         'business_email' => $user->email,
//         'state' => $preApproved->state ?? '',
//         'local_government' => $preApproved->local_government ?? '',
//         'approved' => true,
//         'approved_at' => now(),
//         'approved_by' => $approvedBy, // <-- FIXED: Use available admin or null
//         'status' => 'active',
//         'registration_source' => 'pre_approved',
//     ]);
    
//     $user->update(['shop_id' => $shop->id]);
// }

// // Mark pre-approval as used
// if ($preApproved) {
//     if ($approvalSource === 'admin') {
//         $preApproved->markAsUsed($user->id);
//     } else {
//         // Mark union pre-approval as used
//         $preApproved->update([
//             'status' => 'used',
//             'used_at' => now(),
//             'used_by' => $user->id,
//         ]);
//     }
// }
// Auto-create shop profile for pre-approved shop owners
if ($preApproved && $request->user_type === 'shop_owner') {
    // Find the first available admin user for approved_by
    $adminUser = User::where('user_type', User::TYPE_ADMIN)->first();
    $approvedBy = $adminUser ? $adminUser->id : null;
    
    $shop = \App\Models\Shop::create([
        'user_id' => $user->id,
        'shop_name' => $preApproved->shop_name ?? ($user->name . "'s Shop"),
        'owner_full_name' => $user->name,
        'business_address' => $preApproved->business_address ?? 'Address to be updated',
        'business_phone_1' => $preApproved->business_phone ?? $user->phone_number,
        'business_phone_2' => $user->secondary_phone,
        'business_email' => $user->email,
        'country' => 'Nigeria',
        'state' => $preApproved->state ?? 'Lagos',
        'local_government' => $preApproved->local_government ?? 'Lagos Island',
        'approved' => true,  // ← AUTO-APPROVE
        'approved_at' => now(),
        'approved_by' => $approvedBy,
        'status' => 'active', // ← ACTIVE STATUS
        'registration_source' => 'pre_approved',
        'terms_and_conditions' => 'Pre-approved registration - terms accepted'
    ]);
    
    // Mark the user as having a shop
    $user->update(['shop_id' => $shop->id]);
    
    // Mark pre-approval as used
    $preApproved->update([
        'status' => 'used',
        'used_at' => now(),
        'used_by_user_id' => $user->id
    ]);
}
        event(new Registered($user));

        // Send welcome email
        try {
            \Log::info('User registered successfully: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Registration process error: ' . $e->getMessage());
        }

        // Redirect to login with appropriate message
        $message = $preApproved 
            ? 'Account created and activated successfully! You can now log in and start using the system.'
            : 'Account created successfully! Please log in with your credentials.';
            
        return redirect()->route('login')->with('success', $message);
    }
    /**
     * Clean phone number format.
     */
   /**
     * Clean phone number format.
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        
        // Ensure Nigerian numbers start with proper format
        if (strlen($cleaned) === 11 && substr($cleaned, 0, 1) === '0') {
            $cleaned = '+234' . substr($cleaned, 1);
        } elseif (strlen($cleaned) === 10) {
            $cleaned = '+234' . $cleaned;
        } elseif (!str_starts_with($cleaned, '+234') && strlen($cleaned) === 10) {
            $cleaned = '+234' . $cleaned;
        }
        
        return $cleaned;
    }
}
