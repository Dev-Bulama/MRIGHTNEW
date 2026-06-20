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
    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user->isUnion()) {
    //         abort(403, 'Access denied. Union account required.');
    //     }

    //     $query = PreApproval::where('created_by', $user->id)
    //                       ->orWhereIn('state', $user->assigned_states ?? []);

    //     // Filter by status
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     // Filter by state
    //     if ($request->filled('state')) {
    //         $query->where('state', $request->state);
    //     }

    //     // Search
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('first_name', 'like', "%{$search}%")
    //               ->orWhere('last_name', 'like', "%{$search}%")
    //               ->orWhere('email', 'like', "%{$search}%")
    //               ->orWhere('phone_number', 'like', "%{$search}%");
    //         });
    //     }

    //     $preApprovals = $query->latest()->paginate(15);

    //     $stats = [
    //         'total' => PreApproval::where('created_by', $user->id)->count(),
    //         'used' => PreApproval::where('created_by', $user->id)->where('status', 'used')->count(),
    //         'pending' => PreApproval::where('created_by', $user->id)->where('status', 'pending')->count(),
    //         'expired' => PreApproval::where('created_by', $user->id)->where('status', 'expired')->count(),
    //     ];

    //     return view('union.pre-approvals.index', compact('preApprovals', 'stats'));
    // }
/**
 * Display listing of pre-approvals for this union
 */
public function index(Request $request)
{
    $user = Auth::user();

    if (!$user->isUnion()) {
        abort(403, 'Access denied. Union account required.');
    }

    $query = PreApproval::where('created_by', $user->id);

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

    // FIXED: Pass available states to view
    $availableStates = $user->assigned_states ?? [];

    return view('union.pre-approvals.index', compact('preApprovals', 'stats', 'availableStates'));
}
    /**
     * Show create form
     */
     /**
 * Show create form
 */
public function create()
{
    $user = Auth::user();

    if (!$user->isUnion()) {
        abort(403, 'Access denied.');
    }

    // FIXED: Ensure states are properly passed
    $states = $user->assigned_states ?? [];
    
    if (empty($states)) {
        return redirect()->route('union.pre-approvals.index')
                       ->with('error', 'You have no assigned states. Contact admin to assign locations to your account.');
    }
    
    return view('union.pre-approvals.create', compact('states'));
}
    // public function create()
    // {
    //     $user = Auth::user();

    //     if (!$user->isUnion()) {
    //         abort(403, 'Access denied.');
    //     }

    //     $states = $user->assigned_states ?? [];
        
    //     return view('union.pre-approvals.create', compact('states'));
    // }

    /**
     * Store new pre-approval
     */
    /**
 * Store new pre-approval
 */
public function store(Request $request)
{
    $user = Auth::user();

    if (!$user->isUnion()) {
        abort(403, 'Access denied.');
    }

    // FIXED: Proper validation rules
    try {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:pre_approvals,email',
            'phone_number' => 'required|string|max:20|unique:pre_approvals,phone_number',
            'state' => [
                'required', 
                'string', 
                function ($attribute, $value, $fail) use ($user) {
                    if (!in_array($value, $user->assigned_states ?? [])) {
                        $fail('The selected state is not in your assigned locations.');
                    }
                }
            ],
            'local_government' => 'required|string|max:255',
            'shop_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:today',
        ]);

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
                       ->with('success', 'Pre-approval created successfully! The user can now register using their email or phone number.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->errors())->withInput()
                    ->with('error', 'Please correct the errors below and try again.');
    } catch (\Exception $e) {
        return back()->withInput()
                    ->with('error', 'Failed to create pre-approval. Please try again. Error: ' . $e->getMessage());
    }
}
    /**
     * Import pre-approvals from CSV
     */
    // public function import(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user->isUnion()) {
    //         abort(403, 'Access denied.');
    //     }

    //     $request->validate([
    //         'csv_file' => 'required|file|mimes:csv,txt|max:2048',
    //     ]);

    //     try {
    //         $file = $request->file('csv_file');
    //         $data = array_map('str_getcsv', file($file->path()));
    //         $headers = array_shift($data);

    //         $imported = 0;
    //         $errors = [];

    //         foreach ($data as $row) {
    //             if (count($row) !== count($headers)) {
    //                 continue;
    //             }

    //             $rowData = array_combine($headers, $row);
                
    //             // Validate state assignment
    //             if (!in_array($rowData['state'] ?? '', $user->assigned_states ?? [])) {
    //                 $errors[] = "Row with email {$rowData['email']} - State not in your assigned locations.";
    //                 continue;
    //             }

    //             PreApproval::create([
    //                 'first_name' => $rowData['first_name'] ?? '',
    //                 'last_name' => $rowData['last_name'] ?? '',
    //                 'email' => $rowData['email'] ?? '',
    //                 'phone_number' => $rowData['phone_number'] ?? '',
    //                 'state' => $rowData['state'] ?? '',
    //                 'local_government' => $rowData['local_government'] ?? '',
    //                 'shop_name' => $rowData['shop_name'] ?? null,
    //                 'business_address' => $rowData['business_address'] ?? null,
    //                 'expires_at' => Carbon::now()->addMonths(6),
    //                 'created_by' => $user->id,
    //                 'status' => 'pending',
    //                 'approval_code' => 'UNION-' . strtoupper(uniqid()),
    //             ]);

    //             $imported++;
    //         }

    //         $message = "Successfully imported {$imported} pre-approvals.";
    //         if (!empty($errors)) {
    //             $message .= ' ' . count($errors) . ' rows had errors.';
    //         }

    //         return redirect()->route('union.pre-approvals.index')
    //                       ->with('success', $message);

    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Failed to import pre-approvals. Please check your file format.');
    //     }
    // }
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
        $handle = fopen($file->path(), 'r');
        
        if ($handle === false) {
            return back()->with('error', 'Could not read the uploaded file.');
        }

        // Skip header row
        $headers = fgetcsv($handle);
        
        $imported = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            try {
                // Validate row has minimum required columns
                if (count($row) < 6) {
                    $errors[] = "Row {$rowNumber}: Insufficient columns (minimum 6 required)";
                    continue;
                }

                $rowData = [
                    'first_name' => trim($row[0] ?? ''),
                    'last_name' => trim($row[1] ?? ''),
                    'email' => trim($row[2] ?? ''),
                    'phone_number' => trim($row[3] ?? ''),
                    'state' => trim($row[4] ?? ''),
                    'local_government' => trim($row[5] ?? ''),
                    'shop_name' => !empty(trim($row[6] ?? '')) ? trim($row[6]) : null,
                    'business_address' => !empty(trim($row[7] ?? '')) ? trim($row[7]) : null,
                ];
                
                // Validate required fields
                if (empty($rowData['first_name']) || empty($rowData['last_name']) || 
                    empty($rowData['email']) || empty($rowData['phone_number']) ||
                    empty($rowData['state']) || empty($rowData['local_government'])) {
                    $errors[] = "Row {$rowNumber}: Missing required fields (first_name, last_name, email, phone_number, state, local_government)";
                    continue;
                }
                
                // Validate email format
                if (!filter_var($rowData['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: Invalid email format ({$rowData['email']})";
                    continue;
                }
                
                // Check if email already exists
                if (PreApproval::where('email', $rowData['email'])->exists()) {
                    $errors[] = "Row {$rowNumber}: Email {$rowData['email']} already exists";
                    continue;
                }
                
                // Check if phone already exists
                if (PreApproval::where('phone_number', $rowData['phone_number'])->exists()) {
                    $errors[] = "Row {$rowNumber}: Phone number {$rowData['phone_number']} already exists";
                    continue;
                }
                
                // Validate state assignment
                if (!in_array($rowData['state'], $user->assigned_states ?? [])) {
                    $errors[] = "Row {$rowNumber}: State '{$rowData['state']}' is not in your assigned locations";
                    continue;
                }

                // Create pre-approval
                PreApproval::create([
                    'first_name' => $rowData['first_name'],
                    'last_name' => $rowData['last_name'],
                    'email' => $rowData['email'],
                    'phone_number' => $rowData['phone_number'],
                    'state' => $rowData['state'],
                    'local_government' => $rowData['local_government'],
                    'shop_name' => $rowData['shop_name'],
                    'business_address' => $rowData['business_address'],
                    'expires_at' => Carbon::now()->addMonths(6),
                    'created_by' => $user->id,
                    'status' => 'pending',
                    'approval_code' => 'UNION-' . strtoupper(uniqid()),
                ]);

                $imported++;
                
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        $message = "Successfully imported {$imported} pre-approvals.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " rows had errors.";
        }
        
        return redirect()->route('union.pre-approvals.index')
                       ->with('success', $message)
                       ->with('import_errors', $errors);
                       
    } catch (\Exception $e) {
        return back()->with('error', 'Import failed: ' . $e->getMessage());
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
            'john.doe@example.com',
            '08012345678',
            'Lagos',
            'Ikeja',
            'John\'s Electronics',
            '123 Main Street, Ikeja, Lagos'
        ],
        [
            'Jane',
            'Smith', 
            'jane.smith@example.com',
            '08087654321',
            'Kaduna',
            'Kaduna North',
            'Jane\'s Store',
            '456 Market Road, Kaduna'
        ]
    ];

    $filename = 'union-pre-approval-template-' . now()->format('Y-m-d');
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
    ];

    $callback = function() use ($headers, $sampleData) {
        $file = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($file, [
            'first_name',
            'last_name', 
            'email',
            'phone_number',
            'state',
            'local_government',
            'shop_name',
            'business_address'
        ]);
        
        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($file, $row);
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
    // public function template()
    // {
    //     $headers = [
    //         'first_name',
    //         'last_name', 
    //         'email',
    //         'phone_number',
    //         'state',
    //         'local_government',
    //         'shop_name',
    //         'business_address'
    //     ];

    //     $sampleData = [
    //         [
    //             'John',
    //             'Doe',
    //             'john@example.com',
    //             '08012345678',
    //             'Lagos',
    //             'Ikeja',
    //             'John\'s Shop',
    //             '123 Main Street, Ikeja'
    //         ]
    //     ];

    //     $filename = 'pre-approval-template';
    //     return $this->exportToCsv(array_merge([$headers], $sampleData), $filename);
    // }
    /**
 * Get LGAs for selected state (AJAX)
 */
/**
 * Get LGAs for selected state (AJAX) - Union Assigned Only
 */
public function getLgas(Request $request)
{
    try {
        $user = Auth::user();
        $state = $request->input('state');
        
        if (!$state) {
            return response()->json(['success' => false, 'message' => 'State is required'], 400);
        }

        // Check if user can manage this state
        $userAssignedStates = $user->assigned_states ?? [];
        if (!in_array($state, $userAssignedStates)) {
            return response()->json(['success' => false, 'message' => 'Access denied for this state'], 403);
        }

        // Get user's assigned LGAs
        $userAssignedLgas = $user->assigned_lgas ?? [];
        
        // If user has NO assigned LGAs at all, they can't create pre-approvals
        if (empty($userAssignedLgas)) {
            return response()->json([
                'success' => true,
                'lgas' => [],
                'message' => 'No LGAs assigned to your account. Contact admin to assign specific LGAs.',
                'debug_info' => [
                    'state' => $state,
                    'user_assigned_lgas' => [],
                    'user_assigned_states' => $userAssignedStates,
                ]
            ]);
        }

        // Get ALL LGAs for the selected state using NigeriaData
        try {
            if (class_exists('\App\Services\NigeriaData')) {
                $allStateLgas = \App\Services\NigeriaData::lgas($state);
            } elseif (class_exists('\App\Helpers\NigeriaData')) {
                $allStateLgas = \App\Helpers\NigeriaData::lgas($state);
            } else {
                throw new \Exception('NigeriaData service not found');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Unable to load LGA data. NigeriaData service unavailable: ' . $e->getMessage()
            ], 500);
        }
        
        // Filter to show ONLY LGAs that are:
        // 1. In the selected state (from NigeriaData) AND 
        // 2. Assigned to this union user
        $availableLgas = array_intersect($allStateLgas, $userAssignedLgas);
        
        if (empty($availableLgas)) {
            return response()->json([
                'success' => true,
                'lgas' => [],
                'message' => "No LGAs assigned to you in {$state}. Contact admin to assign LGAs in this state.",
                'debug_info' => [
                    'state' => $state,
                    'user_assigned_lgas' => $userAssignedLgas,
                    'all_state_lgas_count' => count($allStateLgas),
                    'intersection_result' => array_values($availableLgas)
                ]
            ]);
        }

        // Convert to indexed array and sort
        $availableLgas = array_values($availableLgas);
        sort($availableLgas);

        return response()->json([
            'success' => true,
            'lgas' => $availableLgas,
            'message' => count($availableLgas) . " LGA(s) available in {$state}",
            'debug_info' => [
                'state' => $state,
                'user_assigned_states' => $userAssignedStates,
                'user_assigned_lgas' => $userAssignedLgas,
                'all_state_lgas_count' => count($allStateLgas),
                'available_lgas_count' => count($availableLgas),
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Error loading LGAs: ' . $e->getMessage()
        ], 500);
    }
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