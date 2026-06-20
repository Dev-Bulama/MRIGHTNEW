<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if users already exist to avoid duplicates
        if (User::where('email', 'admin@mright.com')->exists()) {
            $this->command->info('Test users already exist. Skipping...');
            return;
        }

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@mright.com',
            'phone_number' => '08012345678',
            'user_type' => 'admin',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'verified_at' => now(),
        ]);

        // Create Shop Owner User with Shop
        $shopOwner = User::create([
            'name' => 'John Smith',
            'email' => 'shop@mright.com',
            'phone_number' => '08098765432',
            'user_type' => 'shop_owner',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'verified_at' => now(),
        ]);

        // Create approved shop for the shop owner
        Shop::create([
            'user_id' => $shopOwner->id,
            'shop_name' => 'TechHub Electronics',
            'owner_full_name' => 'John Smith',
            'business_address' => '123 Technology Street, Computer Village',
            'business_phone_1' => '08098765432',
            'business_phone_2' => '08087654321',
            'business_email' => 'shop@mright.com',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'local_government' => 'Ikeja',
            'status' => 'active',
            'approved' => true,
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        // Create Pending Shop Owner (not yet approved)
        $pendingShopOwner = User::create([
            'name' => 'Jane Doe',
            'email' => 'pending@mright.com',
            'phone_number' => '08055544433',
            'user_type' => 'shop_owner',
            'status' => 'pending',
            'password' => Hash::make('password123'),
            'verified_at' => now(),
        ]);

        // Create pending shop
        Shop::create([
            'user_id' => $pendingShopOwner->id,
            'shop_name' => 'Mobile World',
            'owner_full_name' => 'Jane Doe',
            'business_address' => '456 Mobile Plaza, Alaba Market',
            'business_phone_1' => '08055544433',
            'business_email' => 'pending@mright.com',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'local_government' => 'Ojo',
            'status' => 'pending_approval',
            'approved' => false,
        ]);

        // Create Customer User
        $customer = User::create([
            'name' => 'Mary Johnson',
            'email' => 'customer@mright.com',
            'phone_number' => '08066677788',
            'user_type' => 'customer',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'verified_at' => now(),
        ]);

        $this->command->info('✅ Test users created successfully!');
        $this->command->line('');
        $this->command->line('🎯 === TEST LOGIN CREDENTIALS ===');
        $this->command->line('');
        $this->command->line('🔐 ADMIN USER:');
        $this->command->line('   Email: admin@mright.com');
        $this->command->line('   Password: password123');
        $this->command->line('   Access: Full admin dashboard, shop management');
        $this->command->line('');
        $this->command->line('🏪 SHOP OWNER (Approved):');
        $this->command->line('   Email: shop@mright.com');
        $this->command->line('   Password: password123');
        $this->command->line('   Access: Can generate receipts, view analytics');
        $this->command->line('   Shop: TechHub Electronics (Active)');
        $this->command->line('');
        $this->command->line('⏳ SHOP OWNER (Pending):');
        $this->command->line('   Email: pending@mright.com');
        $this->command->line('   Password: password123');
        $this->command->line('   Access: Limited dashboard, awaiting approval');
        $this->command->line('   Shop: Mobile World (Pending)');
        $this->command->line('');
        $this->command->line('👤 CUSTOMER:');
        $this->command->line('   Email: customer@mright.com');
        $this->command->line('   Password: password123');
        $this->command->line('   Access: View receipts, search functionality');
        $this->command->line('');
        $this->command->line('🌐 Access at: http://localhost:8000/login');
        $this->command->line('📋 Test page: http://localhost:8000/test-credentials');
    }
}