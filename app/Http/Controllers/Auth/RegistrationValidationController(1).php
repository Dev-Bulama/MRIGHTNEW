<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PreApprovedUser;
use Illuminate\Http\Request;

class RegistrationValidationController extends Controller
{
    /**
     * Check if user details are pre-approved.
     */
    public function checkPreApproval(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'user_type' => 'required|in:customer,shop_owner'
        ]);

        $email = $request->email;
        $phone = preg_replace('/[^0-9+]/', '', $request->phone_number);
        $userType = $request->user_type;

        // Check if user is pre-approved
        $preApproved = PreApprovedUser::where('status', 'pending')
            ->where('user_type', $userType)
            ->where(function($query) use ($email, $phone) {
                $query->where('email', $email)
                      ->orWhere(function($q) use ($phone) {
                          $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
                          $q->where('phone_number', 'like', "%{$cleanPhone}%")
                            ->orWhere('phone_number', $phone);
                      });
            })
            ->first();

        if ($preApproved) {
            return response()->json([
                'approved' => true,
                'message' => 'Great! Your details are pre-approved. You can continue with registration.',
                'data' => [
                    'shop_name' => $preApproved->shop_name,
                    'business_address' => $preApproved->business_address,
                    'state' => $preApproved->state,
                    'local_government' => $preApproved->local_government,
                ]
            ]);
        } else {
            return response()->json([
                'approved' => false,
                'message' => 'Your details are not yet approved for registration. Please contact your market chairman to upload your details before proceeding.',
                'contact_info' => 'Contact your AMPAT Executive or M-right state Coordinator for approval.'
            ]);
        }
    }
}