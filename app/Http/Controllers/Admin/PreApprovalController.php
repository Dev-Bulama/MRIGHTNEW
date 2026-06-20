<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreApprovedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PreApprovalController extends Controller
{
    /**
     * Display the pre-approval management page.
     */
    public function index(Request $request)
    {
        $query = PreApprovedUser::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('shop_name', 'like', "%{$search}%");
            });
        }

        $preApprovals = $query->latest()->paginate(15);

        $stats = [
            'total' => PreApprovedUser::count(),
            'pending' => PreApprovedUser::where('status', 'pending')->count(),
            'used' => PreApprovedUser::where('status', 'used')->count(),
            'expired' => PreApprovedUser::where('status', 'expired')->count(),
        ];

        return view('admin.pre-approvals.index', compact('preApprovals', 'stats'));
    }

    /**
     * Show form to manually add pre-approved user.
     */
    public function create()
    {
        return view('admin.pre-approvals.create');
    }

    /**
     * Store manually added pre-approved user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:pre_approved_users,email',
            'phone_number' => 'required|string|unique:pre_approved_users,phone_number',
            'secondary_phone' => 'nullable|string',
            'user_type' => 'required|in:customer,shop_owner',
            'shop_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string',
            'business_phone' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'local_government' => 'nullable|string|max:100',
        ]);

        $validated['status'] = 'pending';
        $validated['import_metadata'] = [
            'added_manually' => true,
            'added_by' => auth()->id(),
            'added_at' => now(),
        ];

        PreApprovedUser::create($validated);

        return redirect()->route('admin.pre-approvals.index')
            ->with('success', 'Pre-approved user added successfully!');
    }

    /**
     * Import pre-approved users from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'r');
            
            // Skip header row
            $header = fgetcsv($handle);
            
            $imported = 0;
            $errors = [];
            $rowNumber = 1; // Start from 1 (header is row 0)
            
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }
                    
                    // Map CSV columns to database fields
                    $userData = [
                        'first_name' => trim($row[0] ?? ''),
                        'last_name' => trim($row[1] ?? ''),
                        'email' => trim($row[2] ?? ''),
                        'phone_number' => trim($row[3] ?? ''),
                        'secondary_phone' => !empty(trim($row[4] ?? '')) ? trim($row[4]) : null,
                        'user_type' => trim($row[5] ?? 'shop_owner'),
                        'shop_name' => !empty(trim($row[6] ?? '')) ? trim($row[6]) : null,
                        'business_address' => !empty(trim($row[7] ?? '')) ? trim($row[7]) : null,
                        'business_phone' => !empty(trim($row[8] ?? '')) ? trim($row[8]) : null,
                        'state' => !empty(trim($row[9] ?? '')) ? trim($row[9]) : null,
                        'local_government' => !empty(trim($row[10] ?? '')) ? trim($row[10]) : null,
                        'status' => 'pending',
                        'import_metadata' => [
                            'imported_at' => now(),
                            'imported_by' => auth()->id(),
                            'source_file' => $file->getClientOriginalName(),
                            'row_number' => $rowNumber,
                        ]
                    ];
                    
                    // Validate required fields
                    if (empty($userData['first_name']) || empty($userData['last_name']) || 
                        empty($userData['email']) || empty($userData['phone_number'])) {
                        $errors[] = "Row {$rowNumber}: Missing required fields";
                        continue;
                    }
                    
                    // Check for existing email
                    if (PreApprovedUser::where('email', $userData['email'])->exists()) {
                        $errors[] = "Row {$rowNumber}: Email {$userData['email']} already exists";
                        continue;
                    }
                    
                    PreApprovedUser::create($userData);
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
            
            fclose($handle);
            
            $message = "Successfully imported {$imported} users.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " rows had errors.";
            }
            
            return redirect()->route('admin.pre-approvals.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export pre-approved users as CSV.
     */
    public function export()
    {
        $users = PreApprovedUser::orderBy('created_at', 'desc')->get();
        
        $filename = 'pre_approved_users_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Description' => 'File Transfer',
            'Expires' => '0',
            'Pragma' => 'public',
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, [
                'first_name', 'last_name', 'email', 'phone_number', 'secondary_phone',
                'user_type', 'shop_name', 'business_address', 'business_phone',
                'state', 'local_government', 'status', 'created_at', 'used_at'
            ]);
            
            // Add data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->phone_number,
                    $user->secondary_phone,
                    $user->user_type,
                    $user->shop_name,
                    $user->business_address,
                    $user->business_phone,
                    $user->state,
                    $user->local_government,
                    $user->status,
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                    $user->used_at ? $user->used_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }

    /**
     * Download CSV template.
     */
    public function template()
    {
        $filename = 'pre_approval_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Description' => 'File Transfer',
            'Expires' => '0',
            'Pragma' => 'public',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, [
                'first_name', 'last_name', 'email', 'phone_number', 'secondary_phone',
                'user_type', 'shop_name', 'business_address', 'business_phone',
                'state', 'local_government'
            ]);
            
            // Add sample data
            fputcsv($file, [
                'John', 'Doe', 'john.doe@example.com', '08012345678', '08087654321',
                'shop_owner', 'John Electronics', '123 Computer Village, Lagos', '08012345678',
                'Lagos', 'Ikeja'
            ]);
            
            fputcsv($file, [
                'Jane', 'Smith', 'jane.smith@example.com', '08023456789', '',
                'shop_owner', 'Jane Mobile Store', '456 Market Street, Kano', '08023456789',
                'Kano', 'Nasarawa'
            ]);
            
            fputcsv($file, [
                'Michael', 'Johnson', 'michael.j@example.com', '08034567890', '08076543210',
                'shop_owner', 'Mike Tech Hub', '789 Technology Road, Abuja', '08034567890',
                'FCT', 'Abuja Municipal'
            ]);
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }

    /**
     * Delete a pre-approved user.
     */
    public function destroy(PreApprovedUser $preApproval)
    {
        try {
            $preApproval->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pre-approved user deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
}