<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class DefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->command->info('Creating default system roles...');

        // Get admin user to assign as creator (use first admin or create one)
        $adminUser = User::where('user_type', User::TYPE_ADMIN)->first();
        
        if (!$adminUser) {
            $this->command->warn('No admin user found. Creating default admin user...');
            $adminUser = User::create([
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'name' => 'System Administrator',
                'email' => 'admin@ampat.com',
                'password' => bcrypt('password'),
                'user_type' => User::TYPE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]);
            $this->command->info('Created default admin user: admin@ampat.com (password: password)');
        }

        $defaultRoles = Role::defaultRoles();
        $created = 0;
        $skipped = 0;

        foreach ($defaultRoles as $roleData) {
            if (Role::where('name', $roleData['name'])->exists()) {
                $this->command->warn("Role '{$roleData['name']}' already exists. Skipping...");
                $skipped++;
                continue;
            }

            Role::create(array_merge($roleData, [
                'created_by' => $adminUser->id,
            ]));

            $this->command->info("Created role: {$roleData['display_name']}");
            $created++;
        }

        // Create additional specialized roles
        $additionalRoles = [
            [
                'name' => 'regional_union_manager',
                'display_name' => 'Regional Union Manager',
                'description' => 'Manages multiple state unions within a region',
                'permissions' => [
                    'view_shop_owners',
                    'view_shop_statistics',
                    'manage_pre_approvals',
                    'view_pre_approvals',
                    'upload_pre_approvals',
                    'manage_by_state',
                    'view_union_reports',
                ],
                'is_system_role' => true,
            ],
            [
                'name' => 'finance_manager',
                'display_name' => 'Finance Manager',
                'description' => 'Specialized role for handling all financial operations',
                'permissions' => [
                    'approve_payouts',
                    'view_payouts',
                    'process_payments',
                    'view_shop_statistics',
                    'view_union_reports',
                ],
                'is_system_role' => true,
            ],
            [
                'name' => 'operations_manager',
                'display_name' => 'Operations Manager',
                'description' => 'Oversees daily operations and shop owner management',
                'permissions' => [
                    'manage_shop_owners',
                    'view_shop_owners',
                    'view_shop_statistics',
                    'manage_pre_approvals',
                    'view_pre_approvals',
                    'upload_pre_approvals',
                    'view_payouts',
                ],
                'is_system_role' => true,
            ],
            [
                'name' => 'data_analyst',
                'display_name' => 'Data Analyst',
                'description' => 'View-only access for reporting and analytics',
                'permissions' => [
                    'view_shop_owners',
                    'view_shop_statistics',
                    'view_pre_approvals',
                    'view_payouts',
                    'view_union_reports',
                ],
                'is_system_role' => true,
            ],
        ];

        foreach ($additionalRoles as $roleData) {
            if (Role::where('name', $roleData['name'])->exists()) {
                $this->command->warn("Role '{$roleData['name']}' already exists. Skipping...");
                $skipped++;
                continue;
            }

            Role::create(array_merge($roleData, [
                'created_by' => $adminUser->id,
            ]));

            $this->command->info("Created specialized role: {$roleData['display_name']}");
            $created++;
        }

        $this->command->info("Seeder completed: {$created} roles created, {$skipped} skipped.");
        
        // Display available permissions
        $this->command->info("\nAvailable permissions in the system:");
        $permissions = Role::availablePermissions();
        foreach ($permissions as $key => $name) {
            $this->command->line("  • {$key}: {$name}");
        }

        $this->command->info("\nRoles created successfully! You can now:");
        $this->command->line("1. Assign these roles to union users");
        $this->command->line("2. Create custom roles with specific permission combinations");
        $this->command->line("3. Manage role assignments through the admin interface");
    }
}