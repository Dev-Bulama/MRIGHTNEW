<?php
namespace App\Http\Controllers;

use App\Models\AntiTheftPhone;
use App\Models\PhoneSearchOtp;
use App\Models\Receipt;
use App\Models\SearchIntelligence;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class PhoneSearchController extends Controller
{
    public function initiateSearch(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => ['required', 'string', 'min:6', 'max:20', 'regex:/^[A-Za-z0-9\-\/]+$/'],
            'seller_whatsapp' => ['required', 'string', 'regex:/^[0-9+\s\-]{7,20}$/'],
        ]);

        $key = 'phone_search_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json(['success' => false, 'message' => "Too many attempts. Try again in {$seconds} seconds."], 429);
        }
        RateLimiter::hit($key, 300);

        $serialNumber = strtoupper(trim($validated['serial_number']));
        $sellerWhatsapp = preg_replace('/[\s\-]/', '', $validated['seller_whatsapp']);

        // Invalidate any existing unused OTPs for this serial
        PhoneSearchOtp::where('serial_number', $serialNumber)
            ->where('used', false)
            ->where('verified', false)
            ->update(['used' => true]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $sessionToken = Str::random(64);

        PhoneSearchOtp::create([
            'serial_number' => $serialNumber,
            'seller_whatsapp' => $sellerWhatsapp,
            'otp' => $otp,
            'session_token' => $sessionToken,
            'ip_address' => $request->ip(),
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via SMS/WhatsApp
        $this->sendOtp($sellerWhatsapp, $otp, $serialNumber);

        return response()->json([
            'success' => true,
            'session_token' => $sessionToken,
            'message' => "A 6-digit verification code has been sent to {$sellerWhatsapp}. Ask the seller for this code.",
            'expires_in' => 600,
        ]);
    }

    public function verifyOtpAndSearch(Request $request)
    {
        $validated = $request->validate([
            'session_token' => ['required', 'string', 'size:64'],
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $otpRecord = PhoneSearchOtp::where('session_token', $validated['session_token'])
            ->where('used', false)
            ->first();

        if (!$otpRecord) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired session. Please start over.'], 422);
        }

        $otpRecord->increment('attempts');

        if ($otpRecord->attempts > 5) {
            $otpRecord->update(['used' => true]);
            return response()->json(['success' => false, 'message' => 'Too many failed attempts. Please start over.'], 422);
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->update(['used' => true]);
            return response()->json(['success' => false, 'message' => 'Verification code has expired. Please start over.'], 422);
        }

        if ($otpRecord->otp !== $validated['otp']) {
            $remaining = 5 - $otpRecord->attempts;
            return response()->json(['success' => false, 'message' => "Incorrect code. {$remaining} attempts remaining."], 422);
        }

        // OTP valid — mark used and perform lookup
        $otpRecord->update(['used' => true, 'verified' => true]);

        $result = $this->performLookup($otpRecord->serial_number, $otpRecord->seller_whatsapp, $request);

        return response()->json(['success' => true, 'result' => $result]);
    }

    private function performLookup(string $serialNumber, string $sellerWhatsapp, Request $request): array
    {
        $rewardPhone = SystemSetting::get('reward_phone', '08013131313');

        // Check AntiTheftPhone table first
        $antiTheftPhone = AntiTheftPhone::where('serial_number', $serialNumber)->first();

        // Check Receipt table by serial number
        $receipt = Receipt::where('phone_serial_number', $serialNumber)
            ->with(['shop'])
            ->latest()
            ->first();

        $searchResult = 'not_found';
        $responseData = ['reward_phone' => $rewardPhone];

        if ($antiTheftPhone && $antiTheftPhone->status === AntiTheftPhone::STATUS_REPORTED_STOLEN) {
            $searchResult = 'found_missing';
            $responseData = array_merge($responseData, [
                'case' => 2,
                'phone_model' => $antiTheftPhone->phone_brand . ' ' . $antiTheftPhone->phone_model,
                'owner_name' => $antiTheftPhone->current_owner_name ?? 'Reported Owner',
                'owner_phone' => $antiTheftPhone->current_owner_phone,
                'date_reported' => $antiTheftPhone->updated_at->format('M d, Y'),
                'serial_number' => $serialNumber,
            ]);
        } elseif ($receipt && $receipt->is_missing) {
            $searchResult = 'found_missing';
            $responseData = array_merge($responseData, [
                'case' => 2,
                'phone_model' => $receipt->phone_name,
                'owner_name' => $receipt->customer_name,
                'owner_phone' => $receipt->customer_phone,
                'date_reported' => $receipt->missing_reported_at ? $receipt->missing_reported_at->format('M d, Y') : 'Unknown',
                'serial_number' => $serialNumber,
            ]);
        } elseif ($receipt) {
            $searchResult = 'found_not_missing';
            $responseData = array_merge($responseData, [
                'case' => 1,
                'phone_model' => $receipt->phone_name,
                'phone_color' => $receipt->phone_color ?? '',
                'owner_name' => $receipt->customer_name,
                'owner_phone' => $receipt->customer_phone,
                'date_registered' => $receipt->created_at->format('M d, Y'),
                'serial_number' => $serialNumber,
            ]);
        } else {
            $searchResult = 'not_found';
            $responseData = array_merge($responseData, ['case' => 3, 'serial_number' => $serialNumber]);
        }

        // Store intelligence (basic — GPS/camera done client-side via captureIntelligence endpoint)
        try {
            SearchIntelligence::create([
                'serial_number' => $serialNumber,
                'seller_whatsapp' => $sellerWhatsapp,
                'search_result' => $searchResult,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->headers->get('referer'),
                'session_id' => session()->getId(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Intelligence capture failed: ' . $e->getMessage());
        }

        // If missing phone found, notify admin
        if ($searchResult === 'found_missing') {
            $this->notifyAdminMissingPhoneSearch($serialNumber, $request);
        }

        return array_merge($responseData, ['search_result' => $searchResult]);
    }

    public function captureIntelligence(Request $request)
    {
        $data = $request->validate([
            'serial_number' => ['required', 'string'],
            'browser' => ['nullable', 'string', 'max:100'],
            'os' => ['nullable', 'string', 'max:100'],
            'device_type' => ['nullable', 'string', 'max:50'],
            'screen_resolution' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:60'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'network_type' => ['nullable', 'string', 'max:50'],
            'fingerprint' => ['nullable', 'array'],
        ]);

        SearchIntelligence::where('serial_number', $data['serial_number'])
            ->where('ip_address', $request->ip())
            ->whereNull('latitude')
            ->latest()
            ->limit(1)
            ->update([
                'browser' => $data['browser'] ?? null,
                'os' => $data['os'] ?? null,
                'device_type' => $data['device_type'] ?? null,
                'screen_resolution' => $data['screen_resolution'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'network_type' => $data['network_type'] ?? null,
                'browser_fingerprint' => isset($data['fingerprint']) ? json_encode($data['fingerprint']) : null,
            ]);

        return response()->json(['success' => true]);
    }

    public function storeCapture(Request $request)
    {
        $request->validate([
            'serial_number' => ['required', 'string'],
            'image' => ['required', 'string'], // base64
        ]);

        try {
            $imageData = $request->image;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]);
                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    throw new \Exception('base64_decode failed');
                }

                $filename = 'captures/' . date('Y/m/d') . '/' . uniqid('cap_') . '.' . $type;
                Storage::disk('local')->put($filename, $imageData);

                SearchIntelligence::where('serial_number', $request->serial_number)
                    ->where('ip_address', $request->ip())
                    ->latest()
                    ->limit(1)
                    ->update(['image_path' => $filename]);
            }
        } catch (\Exception $e) {
            Log::warning('Camera capture storage failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    private function sendOtp(string $phone, string $otp, string $serialNumber): void
    {
        try {
            $message = "M-Right Anti-Theft: Your verification code is {$otp}. A buyer is verifying phone with serial {$serialNumber}. Share this code ONLY if you are selling this phone. Valid for 10 minutes.";

            // Try SMS service
            $smsService = app(\App\Services\SmsService::class);
            $smsService->send($phone, $message);
        } catch (\Exception $e) {
            Log::warning('OTP send failed: ' . $e->getMessage());
            // OTP is stored in DB — admin can retrieve if needed
        }
    }

    private function notifyAdminMissingPhoneSearch(string $serialNumber, Request $request): void
    {
        try {
            $adminEmail = config('services.antitheft.alert_email', config('mail.from.address'));
            Mail::raw(
                "ALERT: Missing phone searched!\n\nSerial: {$serialNumber}\nIP: {$request->ip()}\nTime: " . now()->format('Y-m-d H:i:s') . "\nUser Agent: {$request->userAgent()}",
                function($msg) use ($adminEmail) {
                    $msg->to($adminEmail)->subject('Missing Phone Search Alert - M-Right Anti-Theft');
                }
            );
        } catch (\Exception $e) {
            Log::warning('Admin notification failed: ' . $e->getMessage());
        }
    }
}
