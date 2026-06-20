<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::with(['createdBy', 'users']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by system roles
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->where('is_system_role', true);
            } elseif ($request->type === 'custom') {
                $query->where('is_system_role', false);
            }
        }

        $roles = $query->latest()->paginate(15);
        
        // Get statistics
        $stats = [
            'total_roles' => Role::count(),
            'system_roles' => Role::where('is_system_role', true)->count(),
            'custom_roles' => Role::where('is_system_role', false)->count(),
            'users_with_roles' => User::whereHas('roles')->count(),
        ];

        return view('admin.roles.index', compact('roles', 'stats'));
    }

    /**
     * Show create role form
     */
    public function create()
    {
        $availablePermissions = Role::availablePermissions();
        
        return view('admin.roles.create', compact('availablePermissions'));
    }

    /**
     * Store new role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::availablePermissions())),
        ], [
            'name.regex' => 'Role name must contain only lowercase letters and underscores.',
            'permissions.required' => 'Please select at least one permission.',
        ]);

        try {
            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
                'permissions' => $validated['permissions'],
                'is_system_role' => false,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.roles.index')
                           ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to create role. Please try again.');
        }
    }

    /**
     * Show role details
     */
    public function show(Role $role)
    {
        $role->load(['createdBy', 'users.shop']);
        
        // Get users with this role
        $usersWithRole = $role->users()->with('shop')->paginate(10);
        
        // Get statistics
        $stats = [
            'total_users' => $role->users()->count(),
            'union_users' => $role->users()->where('user_type', User::TYPE_UNION)->count(),
            'active_users' => $role->users()->where('status', User::STATUS_ACTIVE)->count(),
            'created_date' => $role->created_at,
        ];

        return view('admin.roles.show', compact('role', 'usersWithRole', 'stats'));
    }

    /**
     * Show edit role form
     */
    public function edit(Role $role)
    {
        if ($role->is_system_role) {
            return redirect()->route('admin.roles.show', $role)
                           ->with('warning', 'System roles cannot be edited.');
        }

        $availablePermissions = Role::availablePermissions();
        
        return view('admin.roles.edit', compact('role', 'availablePermissions'));
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        if ($role->is_system_role) {
            return redirect()->route('admin.roles.show', $role)
                           ->with('error', 'System roles cannot be modified.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z_]+$/', Rule::unique('roles', 'name')->ignore($role->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::availablePermissions())),
        ], [
            'name.regex' => 'Role name must contain only lowercase letters and underscores.',
            'permissions.required' => 'Please select at least one permission.',
        ]);

        try {
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
                'permissions' => $validated['permissions'],
            ]);

            return redirect()->route('admin.roles.show', $role)
                           ->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update role. Please try again.');
        }
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        if ($role->is_system_role) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that is assigned to users. Remove all users first.');
        }

        try {
            $role->delete();

            return redirect()->route('admin.roles.index')
                           ->with('success', 'Role deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role. Please try again.');
        }
    }

    /**
     * Assign role to user (AJAX)
     */
    public function assignToUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::find($validated['user_id']);
            
            if ($user->roles()->where('role_id', $role->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has this role.'
                ]);
            }

            $user->assignRole($role, auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role.'
            ], 500);
        }
    }

    /**
     * Remove role from user (AJAX)
     */
    public function removeFromUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::find($validated['user_id']);
            
            if (!$user->roles()->where('role_id', $role->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have this role.'
                ]);
            }

            $user->removeRole($role);

            return response()->json([
                'success' => true,
                'message' => 'Role removed successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove role.'
            ], 500);
        }
    }

    /**
     * Create default system roles
     */
    public function createDefaultRoles()
    {
        try {
            $defaultRoles = Role::defaultRoles();
            $created = 0;

            foreach ($defaultRoles as $roleData) {
                if (!Role::where('name', $roleData['name'])->exists()) {
                    Role::create(array_merge($roleData, [
                        'created_by' => auth()->id(),
                    ]));
                    $created++;
                }
            }

            if ($created > 0) {
                return back()->with('success', "Created {$created} default system roles.");
            } else {
                return back()->with('info', 'All default system roles already exist.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create default roles.');
        }
    }

    /**
     * Export roles data
     */
    public function export(Request $request)
    {
        try {
            $roles = Role::with(['createdBy', 'users'])->get();
            
            $exportData = [];
            foreach ($roles as $role) {
                $exportData[] = [
                    'Name' => $role->name,
                    'Display Name' => $role->display_name,
                    'Description' => $role->description ?? '',
                    'Type' => $role->is_system_role ? 'System' : 'Custom',
                    'Permissions' => implode(', ', $role->permissions ?? []),
                    'Users Count' => $role->users()->count(),
                    'Created By' => $role->createdBy ? $role->createdBy->name : 'System',
                    'Created Date' => $role->created_at->format('Y-m-d H:i:s'),
                ];
            }
            
            return $this->exportToCsv($exportData, 'roles-export');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed. Please try again.');
        }
    }

    /**
     * Get available users for role assignment (AJAX)
     */
    public function getAvailableUsers(Role $role)
    {
        try {
            // Get users that don't have this role
            $users = User::whereNotIn('id', $role->users()->pluck('user_id'))
                        ->where('user_type', '!=', User::TYPE_CUSTOMER) // Exclude customers
                        ->select('id', 'name', 'email', 'user_type')
                        ->limit(50)
                        ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load users.'
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
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}