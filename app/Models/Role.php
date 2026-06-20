<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;



class Role extends Model

{

    use HasFactory;



    protected $fillable = [

        'name',

        'display_name',

        'description',

        'permissions',

        'is_system_role',

        'created_by',

    ];



    protected $casts = [

        'permissions' => 'array',

        'is_system_role' => 'boolean',

    ];



    /**

     * Available permissions list

     */

    public static function getAvailablePermissions(): array

    {

        return [

            // User Management

            'users.view' => 'View Users',

            'users.create' => 'Create Users',

            'users.edit' => 'Edit Users',

            'users.delete' => 'Delete Users',

            'users.suspend' => 'Suspend Users',

            

            // Shop Management

            'shops.view' => 'View Shops',

            'shops.approve' => 'Approve Shops',

            'shops.reject' => 'Reject Shops',

            'shops.delete' => 'Delete Shops',

            'shops.edit' => 'Edit Shops',

            

            // Receipt Management

            'receipts.view' => 'View All Receipts',

            'receipts.delete' => 'Delete Receipts',

            'receipts.edit' => 'Edit Receipts',

            

            // Union Management

            'unions.view' => 'View Unions',

            'unions.create' => 'Create Unions',

            'unions.edit' => 'Edit Unions',

            'unions.delete' => 'Delete Unions',

            'unions.assign_location' => 'Assign Locations to Unions',

            

            // Pre-Approval Management

            'pre_approvals.view' => 'View Pre-Approvals',

            'pre_approvals.create' => 'Create Pre-Approvals',

            'pre_approvals.import' => 'Import Pre-Approvals',

            'pre_approvals.delete' => 'Delete Pre-Approvals',

            

            // Payout Management

            'payouts.view' => 'View Payout Requests',

            'payouts.approve' => 'Approve Payout Requests',

            'payouts.reject' => 'Reject Payout Requests',

            'payouts.process' => 'Process Payments',

            

            // Reports

            'reports.view' => 'View Reports',

            'reports.export' => 'Export Reports',

            'reports.financial' => 'View Financial Reports',

            

            // System Management

            'system.settings' => 'Manage System Settings',

            'system.logos' => 'Manage System Logos',

            'system.health' => 'View System Health',

            

            // Role Management

            'roles.view' => 'View Roles',

            'roles.create' => 'Create Roles',

            'roles.edit' => 'Edit Roles',

            'roles.delete' => 'Delete Roles',

            'roles.assign' => 'Assign Roles to Users',

        ];

    }



    /**

     * Default system roles

     */

    public static function getSystemRoles(): array

    {

        return [

            [

                'name' => 'super_admin',

                'display_name' => 'Super Administrator',

                'description' => 'Full system access with all permissions',

                'permissions' => array_keys(self::getAvailablePermissions()),

                'is_system_role' => true,

            ],

            [

                'name' => 'admin',

                'display_name' => 'Administrator',

                'description' => 'Standard admin with most permissions',

                'permissions' => [

                    'users.view', 'users.create', 'users.edit', 'users.suspend',

                    'shops.view', 'shops.approve', 'shops.reject', 'shops.edit',

                    'receipts.view', 'receipts.edit',

                    'unions.view', 'unions.create', 'unions.edit', 'unions.assign_location',

                    'pre_approvals.view', 'pre_approvals.create', 'pre_approvals.import',

                    'payouts.view', 'payouts.approve', 'payouts.reject',

                    'reports.view', 'reports.export',

                    'roles.view', 'roles.assign',

                ],

                'is_system_role' => true,

            ],

            [

                'name' => 'union_executive',

                'display_name' => 'Union Executive',

                'description' => 'Union executives managing shops in assigned locations',

                'permissions' => [

                    'users.view', 'shops.view', 'shops.approve', 'shops.reject',

                    'receipts.view', 'pre_approvals.view', 'pre_approvals.create',

                    'reports.view', 'payouts.view',

                ],

                'is_system_role' => true,

            ],

            [

                'name' => 'finance_admin',

                'display_name' => 'Finance Administrator',

                'description' => 'Handles financial operations and payouts',

                'permissions' => [

                    'payouts.view', 'payouts.approve', 'payouts.reject', 'payouts.process',

                    'reports.view', 'reports.export', 'reports.financial',

                    'shops.view', 'receipts.view',

                ],

                'is_system_role' => true,

            ],

        ];

    }



    /**

     * Relationships

     */
public function users()
{
    return $this->belongsToMany(User::class, 'user_roles')
                ->withPivot(['assigned_at', 'assigned_by'])
                ->withTimestamps()
                ->using(UserRole::class);
}
public function usersWithRoles()
{
    return $this->belongsToMany(User::class, 'user_roles')
                ->withPivot(['assigned_at', 'assigned_by'])
                ->withTimestamps()
                ->using(UserRole::class); // If you have a pivot model
}

    // public function users(): BelongsToMany

    // {

    //     return $this->belongsToMany(User::class, 'user_roles')

    //                 ->withPivot('assigned_by', 'assigned_at')

    //                 ->withTimestamps();

    // }



    public function createdBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by');
}

// Keep both for backward compatibility if needed
public function creator(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by');
}


    /**

     * Check if role has specific permission

     */

    public function hasPermission(string $permission): bool

    {

        return in_array($permission, $this->permissions ?? []);

    }



    /**

     * Add permission to role

     */

    public function addPermission(string $permission): bool

    {

        if (!$this->hasPermission($permission)) {

            $permissions = $this->permissions ?? [];

            $permissions[] = $permission;

            $this->update(['permissions' => $permissions]);

            return true;

        }

        return false;

    }



    /**

     * Remove permission from role

     */

    public function removePermission(string $permission): bool

    {

        if ($this->hasPermission($permission)) {

            $permissions = array_filter($this->permissions ?? [], fn($p) => $p !== $permission);

            $this->update(['permissions' => array_values($permissions)]);

            return true;

        }

        return false;

    }



    /**

     * Get permission names with display names

     */

/**
 * Get permission names with display names
 */
public function getPermissionLabelsAttribute(): array
{
    $available = self::getAvailablePermissions();
    $labels = [];
    
    foreach ($this->permissions ?? [] as $permission) {
        $labels[$permission] = $available[$permission] ?? ucfirst(str_replace('_', ' ', $permission));
    }
    
    return $labels;
}

/**
 * Get formatted permissions with display names (for blade templates)
 */
// public function getFormattedPermissionsAttribute(): array
// {
//     $available = self::getAvailablePermissions();
//     $formatted = [];
    
//     foreach ($this->permissions ?? [] as $permission) {
//         $formatted[] = [
//             'key' => $permission,
//             'name' => $available[$permission] ?? ucfirst(str_replace('_', ' ', $permission))
//         ];
//     }
    
//     return $formatted;
// }


    // public function getPermissionLabelsAttribute(): array

    // {

    //     $available = self::getAvailablePermissions();

    //     $labels = [];

        

    //     foreach ($this->permissions ?? [] as $permission) {

    //         $labels[$permission] = $available[$permission] ?? $permission;

    //     }

        

    //     return $labels;

    // }



    /**

     * Scopes

     */

    public function scopeSystemRoles($query)

    {

        return $query->where('is_system_role', true);

    }



    public function scopeCustomRoles($query)

    {

        return $query->where('is_system_role', false);

    }



    /**

     * Create system roles if they don't exist

     */

    public static function createSystemRoles(): void

    {

        foreach (self::getSystemRoles() as $roleData) {

            self::firstOrCreate(

                ['name' => $roleData['name']],

                $roleData

            );

        }

    }

    /**

 * Available permissions list

 */

public static function availablePermissions(): array

{

    return [

        // Pre-approval management

        'manage_pre_approvals' => 'Manage Pre-approvals',

        'view_pre_approvals' => 'View Pre-approvals',

        'upload_pre_approvals' => 'Upload Pre-approval Lists',

        

        // Shop owner management

        'manage_shop_owners' => 'Manage Shop Owners',

        'view_shop_owners' => 'View Shop Owners',

        'view_shop_statistics' => 'View Shop Statistics',

        

        // Location management

        'manage_by_state' => 'Manage by State',

        'manage_by_lga' => 'Manage by LGA',

        

        // Union management

        'create_unions' => 'Create Unions',

        'assign_unions' => 'Assign Unions to Locations',

        'view_union_reports' => 'View Union Reports',

        

        // Payout management

        'approve_payouts' => 'Approve Payout Requests',

        'view_payouts' => 'View Payout Requests',

        'process_payments' => 'Process Payments',

        

        // System management

        'manage_roles' => 'Manage Roles & Permissions',

        'system_settings' => 'System Settings',

        

        // Reporting and analytics

        'view_reports' => 'View Reports',

        'export_data' => 'Export Data',

        'view_analytics' => 'View Analytics',

        

        // User management

        'manage_users' => 'Manage Users',

        'view_users' => 'View Users',

        'edit_users' => 'Edit Users',

        

        // Additional permissions

        'manage_logos' => 'Manage System Logos',

        'view_receipts' => 'View Receipts',

        'manage_receipts' => 'Manage Receipts',

    ];

}



/**

 * Default roles for the system

 */

public static function defaultRoles(): array

{

    return [

        [

            'name' => 'state_union_manager',

            'display_name' => 'State Union Manager',

            'description' => 'Manages shop owners within assigned states',

            'permissions' => [

                'view_shop_owners',

                'view_shop_statistics',

                'manage_pre_approvals',

                'view_pre_approvals',

                'upload_pre_approvals',

                'manage_by_state',

                'view_reports',

                'export_data',

            ],

            'is_system_role' => true,

        ],

        [

            'name' => 'lga_union_manager',

            'display_name' => 'LGA Union Manager',

            'description' => 'Manages shop owners within assigned LGAs',

            'permissions' => [

                'view_shop_owners',

                'view_shop_statistics',

                'view_pre_approvals',

                'manage_by_lga',

                'view_reports',

            ],

            'is_system_role' => true,

        ],

        [

            'name' => 'payout_manager',

            'display_name' => 'Payout Manager',

            'description' => 'Manages shop owner payout requests',

            'permissions' => [

                'approve_payouts',

                'view_payouts',

                'process_payments',

                'view_shop_statistics',

                'view_reports',

            ],

            'is_system_role' => true,

        ],

        [

            'name' => 'regional_coordinator',

            'display_name' => 'Regional Coordinator',

            'description' => 'Coordinates multiple unions within a region',

            'permissions' => [

                'view_shop_owners',

                'view_shop_statistics',

                'view_pre_approvals',

                'view_union_reports',

                'view_payouts',

                'view_reports',

                'export_data',

            ],

            'is_system_role' => true,

        ],

    ];

}

/**
 * Get formatted permissions with display names
 */
public function getFormattedPermissionsAttribute(): array
{
    $available = self::getAvailablePermissions();
    $formatted = [];
    
    foreach ($this->permissions ?? [] as $permission) {
        $formatted[] = [
            'key' => $permission,
            'name' => $available[$permission] ?? ucfirst(str_replace('_', ' ', $permission))
        ];
    }
    
    return $formatted;
}

}