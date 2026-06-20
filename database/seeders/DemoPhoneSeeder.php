<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Receipt;
use App\Models\Shop;
use App\Models\User;
use App\Models\AntiTheftPhone;
use Illuminate\Support\Str;

class DemoPhoneSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a demo shop/user
        $user = User::where('user_type', 'shop_owner')->first();
        $shop = Shop::where('approved', true)->first();

        if (!$shop || !$user) {
            $this->command->warn('No approved shop found. Please create a shop owner first, then re-run this seeder.');
            return;
        }

        // ============================================================
        // DEMO PHONE 1 — CASE 1: Found & Verified (Not Missing)
        // Serial: DEMO-IPHONE13-001
        // ============================================================
        Receipt::updateOrCreate(
            ['phone_serial_number' => 'DEMO-IPHONE13-001'],
            [
                'user_id'           => $user->id,
                'shop_id'           => $shop->id,
                'receipt_number'    => 'MR-DEMO-2026-001',
                'customer_name'     => 'Chukwuemeka Obi',
                'customer_phone'    => '08031234567',
                'customer_email'    => 'emeka.obi@example.com',
                'customer_address'  => '14 Lagos Island, Lagos',
                'phone_name'        => 'Apple iPhone 13',
                'phone_color'       => 'Midnight Black',
                'phone_serial_number' => 'DEMO-IPHONE13-001',
                'amount'            => 320000,
                'amount_in_words'   => 'Three Hundred and Twenty Thousand Naira',
                'payment_status'    => 'paid',
                'payment_gateway_status' => 'successful',
                'resale_code'       => 'RESALE001',
                'resale_pin'        => '123456',
                'enable_antitheft'  => true,
                'receipt_type'      => 'sale',
                'status'            => 'active',
                'is_missing'        => false,
                'service_fee'       => 500,
                'generated_at'      => now()->subDays(45),
            ]
        );

        // ============================================================
        // DEMO PHONE 2 — CASE 2: Found & MISSING (Stolen)
        // Serial: DEMO-SAMSUNG-002
        // ============================================================
        $missingReceipt = Receipt::updateOrCreate(
            ['phone_serial_number' => 'DEMO-SAMSUNG-002'],
            [
                'user_id'               => $user->id,
                'shop_id'               => $shop->id,
                'receipt_number'        => 'MR-DEMO-2026-002',
                'customer_name'         => 'Aisha Bello',
                'customer_phone'        => '07052345678',
                'customer_email'        => 'aisha.bello@example.com',
                'customer_address'      => '22 Wuse Zone 3, Abuja',
                'phone_name'            => 'Samsung Galaxy S23',
                'phone_color'           => 'Phantom White',
                'phone_serial_number'   => 'DEMO-SAMSUNG-002',
                'amount'                => 250000,
                'amount_in_words'       => 'Two Hundred and Fifty Thousand Naira',
                'payment_status'        => 'paid',
                'payment_gateway_status'=> 'successful',
                'resale_code'           => 'RESALE002',
                'resale_pin'            => '654321',
                'enable_antitheft'      => true,
                'receipt_type'          => 'sale',
                'status'                => 'active',
                'is_missing'            => true,
                'missing_reported_at'   => now()->subDays(3),
                'missing_notes'         => 'Phone was snatched at Wuse Market on June 17, 2026.',
                'service_fee'           => 500,
                'generated_at'          => now()->subDays(60),
            ]
        );

        // Also mark in anti_theft_phones table
        try {
            AntiTheftPhone::updateOrCreate(
                ['serial_number' => 'DEMO-SAMSUNG-002'],
                [
                    'phone_model'           => 'Galaxy S23',
                    'phone_brand'           => 'Samsung',
                    'phone_color'           => 'Phantom White',
                    'status'                => AntiTheftPhone::STATUS_REPORTED_STOLEN,
                    'current_receipt_id'    => $missingReceipt->id,
                    'current_owner_name'    => 'Aisha Bello',
                    'current_owner_phone'   => '07052345678',
                    'registered_at'         => now()->subDays(60),
                ]
            );
        } catch (\Exception $e) {
            // Column may not exist yet if migration not run
        }

        // ============================================================
        // DEMO PHONE 3 — CASE 1 variant: Tecno with resale history
        // Serial: DEMO-TECNO-003
        // ============================================================
        Receipt::updateOrCreate(
            ['phone_serial_number' => 'DEMO-TECNO-003'],
            [
                'user_id'           => $user->id,
                'shop_id'           => $shop->id,
                'receipt_number'    => 'MR-DEMO-2026-003',
                'customer_name'     => 'Fatima Usman',
                'customer_phone'    => '09063456789',
                'customer_email'    => null,
                'customer_address'  => '5 Ring Road, Kano',
                'phone_name'        => 'Tecno Spark 20',
                'phone_color'       => 'Magic Skin Blue',
                'phone_serial_number' => 'DEMO-TECNO-003',
                'amount'            => 95000,
                'amount_in_words'   => 'Ninety-Five Thousand Naira',
                'payment_status'    => 'paid',
                'payment_gateway_status' => 'offline',
                'resale_code'       => 'RESALE003',
                'resale_pin'        => '246810',
                'enable_antitheft'  => true,
                'receipt_type'      => 'sale',
                'status'            => 'active',
                'is_missing'        => false,
                'service_fee'       => 500,
                'generated_at'      => now()->subDays(20),
            ]
        );

        $this->command->info('✅ Demo phones seeded:');
        $this->command->table(
            ['Serial Number', 'Status', 'Owner', 'Device'],
            [
                ['DEMO-IPHONE13-001', '✅ Verified (Case 1)', 'Chukwuemeka Obi', 'Black iPhone 13'],
                ['DEMO-SAMSUNG-002',  '🔴 MISSING (Case 2)',  'Aisha Bello',      'White Samsung Galaxy S23'],
                ['DEMO-TECNO-003',    '✅ Verified (Case 1)', 'Fatima Usman',     'Blue Tecno Spark 20'],
                ['ANYTHING-ELSE',     '❓ Not Found (Case 3)', '—',               '— (just type any random serial)'],
            ]
        );
    }
}
