<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    /**
     * Display logo management page.
     */
    public function index()
    {
        // $this->authorize('admin');
        
        $logos = [
            'ampat' => SystemLogo::getAmpatLogo(),
            'mright' => SystemLogo::getMrightLogo(),
        ];
        
        return view('admin.logos.index', compact('logos'));
    }

    /**
     * Upload system logo.
     */
    public function uploadSystemLogo(Request $request)
    {
        // $this->authorize('admin');
        
        $request->validate([
            'logo_type' => ['required', 'in:ampat,mright'],
            'logo_file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        try {
            $logo = SystemLogo::uploadLogo(
                $request->logo_type,
                $request->file('logo_file'),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->logo_type) . ' logo uploaded successfully.',
                'logo_url' => $logo->logo_url,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to upload system logo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo. Please try again.',
            ], 500);
        }
    }

    /**
     * Delete system logo.
     */
    public function deleteSystemLogo(Request $request)
    {
        // $this->authorize('admin');
        
        $request->validate([
            'logo_type' => ['required', 'in:ampat,mright'],
        ]);

        try {
            $logo = SystemLogo::getByType($request->logo_type);
            
            if (!$logo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logo not found.',
                ], 404);
            }

            // Delete file
            if (Storage::disk('public')->exists($logo->logo_path)) {
                Storage::disk('public')->delete($logo->logo_path);
            }

            // Delete record
            $logo->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->logo_type) . ' logo deleted successfully.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to delete system logo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete logo. Please try again.',
            ], 500);
        }
    }

    /**
     * Upload user logo (for shop owners).
     */
    public function uploadUserLogo(Request $request)
    {
        $request->validate([
            'logo_file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        try {
            $user = Auth::user();
            $uploaded = $user->uploadLogo($request->file('logo_file'));

            if ($uploaded) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logo uploaded successfully.',
                    'logo_url' => $user->logo_url,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo.',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Failed to upload user logo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo. Please try again.',
            ], 500);
        }
    }

    /**
     * Delete user logo.
     */
    public function deleteUserLogo()
    {
        try {
            $user = Auth::user();
            $deleted = $user->deleteLogo();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logo deleted successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete logo.',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Failed to delete user logo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete logo. Please try again.',
            ], 500);
        }
    }

    /**
     * Authorize admin access.
     */
    // private function authorize($role)
    // {
    //     if ($role === 'admin' && !Auth::user()->isAdmin()) {
    //         abort(403, 'Admin access required.');
    //     }
    // }
}