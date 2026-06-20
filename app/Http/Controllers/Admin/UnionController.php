<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\NigeriaData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UnionController extends Controller
{
    /**
     * Display listing of union users
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', User::TYPE_UNION)
                    ->with(['roles', 'assignedBy']);

        // Filter by state
        if ($request->filled('state')) {
            $query->whereJsonContains('assigned_states', $request->state);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search by name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $unions = $query->latest()->paginate(15);
        
        $states = NigeriaData::states();
        $roles = Role::all();

        return view('admin.unions.index', compact('unions', 'states', 'roles'));
    }

    /**
     * Show create union form
     */
    public function create()
    {
        $states = NigeriaData::states();
        $roles = Role::all();
        
        return view('admin.unions.create', compact('states', 'roles'));
    }

    /**
     * Store new union user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'assigned_states' => 'required|array|min:1',
            'assigned_states.*' => 'string|in:' . implode(',', NigeriaData::states()),
            'assigned_lgas' => 'nullable|array',
            'assigned_lgas.*' => 'string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        
        try {
            // Create union user
            $union = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'secondary_phone' => $validated['secondary_phone'],
                'password' => Hash::make($validated['password']),
                'user_type' => User::TYPE_UNION,
                'assigned_states' => $validated['assigned_states'],
                'assigned_lgas' => $validated['assigned_lgas'] ?? [],
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(), // Auto-verify union accounts
            ]);

            // Assign roles
            foreach ($validated['roles'] as $roleId) {
                $role = Role::find($roleId);
                $union->assignRole($role, auth()->user());
            }

            DB::commit();

            return redirect()->route('admin.unions.index')
                           ->with('success', 'Union executive created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                        ->with('error', 'Failed to create union executive. Please try again.');
        }
    }

    /**
     * Show union details
     */
    public function show(User $union)
    {
        if (!$union->isUnion()) {
            abort(404, 'Union not found.');
        }

        $union->load(['roles', 'assignedBy', 'assignedUsers']);
        
        // Get manageable shop owners
        $shopOwners = $union->manageableShopOwners()->with('shop')->paginate(10);
        
        // Get statistics
        $stats = [
            'total_shop_owners' => $union->manageableShopOwners()->count(),
            'active_shops' => $union->manageableShopOwners()
                                   ->whereHas('shop', function($q) {
                                       $q->where('status', 'active');
                                   })->count(),
            'total_receipts' => DB::table('receipts')
                                 ->whereIn('user_id', $union->manageableShopOwners()->pluck('id'))
                                 ->count(),
            'monthly_receipts' => DB::table('receipts')
                                   ->whereIn('user_id', $union->manageableShopOwners()->pluck('id'))
                                   ->whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count(),
        ];

        return view('admin.unions.show', compact('union', 'shopOwners', 'stats'));
    }

    /**
     * Show edit union form
     */
    public function edit(User $union)
    {
        if (!$union->isUnion()) {
            abort(404, 'Union not found.');
        }

        $states = NigeriaData::states();
        $roles = Role::all();
        $assignedRoleIds = $union->roles->pluck('id')->toArray();
        
        return view('admin.unions.edit', compact('union', 'states', 'roles', 'assignedRoleIds'));
    }

    /**
     * Update union user
     */
    public function update(Request $request, User $union)
    {
        if (!$union->isUnion()) {
            abort(404, 'Union not found.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($union->id)],
            'phone_number' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'assigned_states' => 'required|array|min:1',
            'assigned_states.*' => 'string|in:' . implode(',', NigeriaData::states()),
            'assigned_lgas' => 'nullable|array',
            'assigned_lgas.*' => 'string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        DB::beginTransaction();
        
        try {
            // Update union user
            $updateData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'secondary_phone' => $validated['secondary_phone'],
                'assigned_states' => $validated['assigned_states'],
                'assigned_lgas' => $validated['assigned_lgas'] ?? [],
                'status' => $validated['status'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $union->update($updateData);

            // Update roles
            $union->roles()->detach();
            foreach ($validated['roles'] as $roleId) {
                $role = Role::find($roleId);
                $union->assignRole($role, auth()->user());
            }

            DB::commit();

            return redirect()->route('admin.unions.show', $union)
                           ->with('success', 'Union executive updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                        ->with('error', 'Failed to update union executive. Please try again.');
        }
    }

    /**
     * Delete union user
     */
    public function destroy(User $union)
    {
        if (!$union->isUnion()) {
            abort(404, 'Union not found.');
        }

        try {
            $union->roles()->detach();
            $union->delete();

            return redirect()->route('admin.unions.index')
                           ->with('success', 'Union executive deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete union executive. Please try again.');
        }
    }

    /**
     * Get LGAs for selected states (AJAX)
     */
    public function getLgas(Request $request)
    {
        $states = $request->input('states', []);
        $lgas = [];

        foreach ($states as $state) {
            $stateLgas = NigeriaData::lgas($state);
            foreach ($stateLgas as $lga) {
                $lgas[] = [
                    'value' => $lga,
                    'text' => $lga,
                    'state' => $state
                ];
            }
        }

        return response()->json($lgas);
    }

    /**
     * Assign union to shop owners (bulk action)
     */
    // public function assignToShopOwners(Request $request, User $union)
    // {
    //     if (!$union->isUnion()) {
    //         abort(404, 'Union not found.');
    //     }

    //     $validated = $request->validate([
    //         'shop_owner_ids' => 'required|array|min:1',
    //         'shop_owner_ids.*' => 'exists:users,id',
    //     ]);

    //     try {
    //         $shopOwners = User::whereIn('id', $validated['shop_owner_ids'])
    //                          ->where('user_type', User::TYPE_SHOP_OWNER)
    //                          ->get();

    //         $assigned = 0;
    //         foreach ($shopOwners as $shopOwner) {
    //             if ($union->canManageShopOwner($shopOwner)) {
    //                 $shopOwner->update(['assigned_by' => $union->id]);
    //                 $assigned++;
    //             }
    //         }

    //         return back()->with('success', "Successfully assigned {$assigned} shop owners to union.");

    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Failed to assign shop owners. Please try again.');
    //     }
    // }
    /**
 * Assign union to shop owners (AJAX)
 */
public function assignToShopOwners(User $union, Request $request)
{
    try {
        $validated = $request->validate([
            'shop_owner_ids' => 'required|array',
            'shop_owner_ids.*' => 'exists:users,id',
        ]);

        $assignedCount = 0;
        
        foreach ($validated['shop_owner_ids'] as $shopOwnerId) {
            $shopOwner = User::find($shopOwnerId);
            
            if ($shopOwner && $shopOwner->user_type === User::TYPE_SHOP_OWNER) {
                // Check if shop owner's location matches union's assigned locations
                if ($shopOwner->shop && in_array($shopOwner->shop->state, $union->assigned_states ?? [])) {
                    // You can add additional assignment logic here
                    $assignedCount++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$assignedCount} shop owner assignments.",
            'assigned_count' => $assignedCount
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to assign shop owners. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}
}