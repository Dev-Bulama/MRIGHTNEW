<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreApprovedUser;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PreApprovedUsersImport;
use App\Exports\PreApprovedUsersExport;

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
     * Show import form.
     */
    public function create()
    {
        return view('admin.pre-approvals.create');
    }

    /**
     * Import pre-approved users from Excel/CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new PreApprovedUsersImport, $request->file('file'));
            
            return redirect()->route('admin.pre-approvals.index')
                ->with('success', 'Pre-approved users imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export pre-approved users.
     */
    public function export()
    {
        return Excel::download(new PreApprovedUsersExport, 'pre-approved-users.xlsx');
    }

    /**
     * Manually add a pre-approved user.
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

        PreApprovedUser::create($validated);

        return redirect()->route('admin.pre-approvals.index')
            ->with('success', 'Pre-approved user added successfully!');
    }

    /**
     * Delete a pre-approved user.
     */
    public function destroy(PreApprovedUser $preApproval)
    {
        $preApproval->delete();

        return redirect()->route('admin.pre-approvals.index')
            ->with('success', 'Pre-approved user deleted successfully!');
    }
    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'first_name',
            'last_name', 
            'email',
            'phone_number',
            'secondary_phone',
            'user_type',
            'shop_name',
            'business_address',
            'business_phone',
            'state',
            'local_government'
        ];

        $sampleData = [
            [
                'John',
                'Doe',
                'john.doe@example.com',
                '08012345678',
                '08087654321',
                'shop_owner',
                'John Electronics',
                '123 Computer Village, Lagos',
                '08012345678',
                'Lagos',
                'Ikeja'
            ],
            [
                'Jane',
                'Smith',
                'jane.smith@example.com',
                '08023456789',
                '',
                'shop_owner',
                'Jane Mobile Store',
                '456 Market Street, Kano',
                '08023456789',
                'Kano',
                'Nasarawa'
            ]
        ];

        $filename = 'pre_approval_template.csv';
        
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Add headers
        fputcsv($handle, $headers);
        
        // Add sample data
        foreach ($sampleData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }
}