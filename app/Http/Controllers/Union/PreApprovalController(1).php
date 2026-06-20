<?php

namespace App\Http\Controllers\Union;

use App\Http\Controllers\Controller;
use App\Models\PreApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PreApprovalController extends Controller
{
    /**
     * Display listing of pre-approvals for this union
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied. Union account required.');
        }

        $query = PreApproval::where('created_by', $user->id)
                           ->orWhereIn('state', $user->assigned_states ?? []);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $preApprovals = $query->latest()->paginate(15);

        $stats = [
            'total' => PreApproval::where('created_by', $user->id)->count(),
            'used' => PreApproval::where('created_by', $user->id)->where('status', 'used')->count(),
            'pending' => PreApproval::where('created_by', $user->id)->where('status', 'pending')->count(),
            'expired' => PreApproval::where('created_by', $user->id)->where('status', 'expired')->count(),
        ];

        return view('union.pre-approvals.index', compact('preApprovals', 'stats'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied.');
        }

        $states = $user->assigned_states ?? [];
        
        return view('union.pre-approvals.create', compact('states'));
    }

    /**
     * Store new pre-approval
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:pre_approvals,email',
            'phone_number' => 'required|string|max:20',
            'state' => 'required|string|in:' . implode(',', $user->assigned_states ?? []),
            'local_government' => 'required|string',
            'shop_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:today',
        ]);

        try {
            PreApproval::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'state' => $validated['state'],
                'local_government' => $validated['local_government'],
                'shop_name' => $validated['shop_name'],
                'business_address' => $validated['business_address'],
                'expires_at' => $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : Carbon::now()->addMonths(6),
                'created_by' => $user->id,
                'status' => 'pending',
                'approval_code' => 'UNION-' . strtoupper(uniqid()),
            ]);

            return redirect()->route('union.pre-approvals.index')
                           ->with('success', 'Pre-approval created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to create pre-approval. Please try again.');
        }
    }

    /**
     * Import pre-approvals from CSV
     */
    public function import(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('csv_file');
            $data = array_map('str_getcsv', file($file->path()));
            $headers = array_shift($data);

            $imported = 0;
            $errors = [];

            foreach ($data as $row) {
                if (count($row) !== count($headers)) {
                    continue;
                }

                $rowData = array_combine($headers, $row);
                
                // Validate state assignment
                if (!in_array($rowData['state'] ?? '', $user->assigned_states ?? [])) {
                    $errors[] = "Row with email {$rowData['email']} - State not in your assigned locations.";
                    continue;
                }

                PreApproval::create([
                    'first_name' => $rowData['first_name'] ?? '',
                    'last_name' => $rowData['last_name'] ?? '',
                    'email' => $rowData['email'] ?? '',
                    'phone_number' => $rowData['phone_number'] ?? '',
                    'state' => $rowData['state'] ?? '',
                    'local_government' => $rowData['local_government'] ?? '',
                    'shop_name' => $rowData['shop_name'] ?? null,
                    'business_address' => $rowData['business_address'] ?? null,
                    'expires_at' => Carbon::now()->addMonths(6),
                    'created_by' => $user->id,
                    'status' => 'pending',
                    'approval_code' => 'UNION-' . strtoupper(uniqid()),
                ]);

                $imported++;
            }

            $message = "Successfully imported {$imported} pre-approvals.";
            if (!empty($errors)) {
                $message .= ' ' . count($errors) . ' rows had errors.';
            }

            return redirect()->route('union.pre-approvals.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import pre-approvals. Please check your file format.');
        }
    }

    /**
     * Export pre-approvals
     */
    public function export()
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied.');
        }

        $preApprovals = PreApproval::where('created_by', $user->id)->get();

        $exportData = [];
        foreach ($preApprovals as $preApproval) {
            $exportData[] = [
                'First Name' => $preApproval->first_name,
                'Last Name' => $preApproval->last_name,
                'Email' => $preApproval->email,
                'Phone' => $preApproval->phone_number,
                'State' => $preApproval->state,
                'LGA' => $preApproval->local_government,
                'Shop Name' => $preApproval->shop_name,
                'Address' => $preApproval->business_address,
                'Status' => ucfirst($preApproval->status),
                'Approval Code' => $preApproval->approval_code,
                'Created Date' => $preApproval->created_at->format('Y-m-d'),
                'Expires Date' => $preApproval->expires_at ? $preApproval->expires_at->format('Y-m-d') : 'N/A',
            ];
        }

        $filename = 'union-pre-approvals-' . now()->format('Y-m-d');
        return $this->exportToCsv($exportData, $filename);
    }

    /**
     * Download CSV template
     */
    public function template()
    {
        $headers = [
            'first_name',
            'last_name', 
            'email',
            'phone_number',
            'state',
            'local_government',
            'shop_name',
            'business_address'
        ];

        $sampleData = [
            [
                'John',
                'Doe',
                'john@example.com',
                '08012345678',
                'Lagos',
                'Ikeja',
                'John\'s Shop',
                '123 Main Street, Ikeja'
            ]
        ];

        $filename = 'pre-approval-template';
        return $this->exportToCsv(array_merge([$headers], $sampleData), $filename);
    }

    /**
     * Helper method to export data to CSV
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if (!empty($data)) {
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data as $row) {
                        fputcsv($file, $row);
                    }
                } else {
                    fputcsv($file, array_keys($data[0]));
                    foreach ($data as $row) {
                        fputcsv($file, $row);
                    }
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}