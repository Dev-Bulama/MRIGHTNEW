<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Receipt;
use App\Models\Shop;
use App\Models\User;
use App\Models\AntiTheftPhone;

class DemoPhoneSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('user_type', 'shop_owner')->first();
        $shop = Shop::where('approved', true)->first();

        if (!$shop || !$user) {
            $this->command->warn('No approved shop found. Please create a shop owner first, then re-run this seeder.');
            return;
        }

        // Helper to build a receipt row — includes ALL required fields
        $base = function(array $data) use ($user, $shop): array {
            return array_merge([
                'user_id'                    => $user->id,
                'shop_id'                    => $shop->id,
                'payment_status'             => 'paid',
                'payment_gateway_status'     => 'successful',
                'enable_antitheft'           => true,
                'receipt_type'               => 'sale',
                'status'                     => 'active',
                'is_missing'                 => false,
                'service_fee'                => 500,
                'resale_code_confirmation'   => $data['resale_code'] ?? '',
                'notes'                      => null,
            ], $data, [
                // phone_serial_confirmation must match phone_serial_number
                'phone_serial_confirmation'  => $data['phone_serial_number'],
            ]);
        };

        // ────────────────────────────────────────────────────────────
        // DEMO 1 — CASE 1: Registered, NOT missing (iPhone 13)
        // Search: DEMO-IPHONE13-001
        // ────────────────────────────────────────────────────────────
        Receipt::updateOrCreate(
            ['phone_serial_number' => 'DEMO-IPHONE13-001'],
            $base([
                'receipt_number'    => 'MR-DEMO-2026-001',
                'customer_name'     => 'Chukwuemeka Obi',
                'customer_phone'    => '08031234567',
                'customer_email'    => 'emeka.obi@example.com',
                'customer_address'  => '14 Lagos Island, Lagos',
                'customer_sex'      => 'male',
                'phone_name'        => 'Apple iPhone 13',
                'phone_color'       => 'Midnight Black',
                'phone_serial_number' => 'DEMO-IPHONE13-001',
                'amount'            => 320000,
                'amount_in_words'   => 'Three Hundred and Twenty Thousand Naira',
                'resale_code'       => 'RESALE001',
                'resale_pin'        => '123456',
                'generated_at'      => now()->subDays(45),
            ])
        );

        // ────────────────────────────────────────────────────────────
        // DEMO 2 — CASE 2: Registered AND MISSING (Samsung Galaxy S23)
        // Search: DEMO-SAMSUNG-002
        // ────────────────────────────────────────────────────────────
        $missingReceipt = Receipt::updateOrCreate(
            ['phone_serial_number' => 'DEMO-SAMSUNG-002'],
            $base([
                'receipt_number'        => 'MR-DEMO-2026-002',
                'customer_name'         => 'Aisha Bello',
                'customer_phone'        => '07052345678',
                'customer_email'        => 'aisha.bello@example.com',
                'customer_address'      => '22 Wuse Zone 3, Abuja',
                'customer_sex'          => 'female',
                'phone_name'            => 'Samsung Galaxy S23',
                'phone_color'           => 'Phantom White',
                'phone_serial_number'   => 'DEMO-SAMSUNG-002',
                'amount'                => 250000,
                'amount_in_words'       => 'Two Hundred and Fifty Thousand Naira',
                'resale_code'           => 'RESALE002',
                'resale_pin'            => '654321',
                'is_missing'            => true,
                'missing_reported_at'   => now()->subDays(3),
                'missing_notes'         => 'Phone was snatched at Wuse Market on June 17, 2026.',
                'generated_at'          => now()->subDays(60),
            ])
        );

        // Mark in anti_theft_phones too (best-effort)
        try {
            AntiTheftPhone::updateOrCreate(
                ['serial_number' => 'DEMO-SAMSUNG-002'],
                [
                    'phone_model'        => 'Galaxy S23',
                    'phone_brand'        => 'Samsung',
                    'phone_color'        => 'Phantom White',
                    'status'             => AntiTheftPhone::STATUS_REPORTED_STOLEN,
                    'current_receipt_id' => $missingReceipt->id,
                    'current_owner_name' => 'Aisha Bello',
                    'current_owner_phone'=> '07052345678',
                    'registered_at'      => now()->subDays(60),
                ]
            );
        } catch (\Exception $e) {
            $this->command->warn('AntiTheftPhone seed skipped: ' . $e->getMessage());
        }

        // ────────────────────────────────────────────────────────────
        // DEMO 3 — CASE 1: Registered, NOT missing (Tecno Spark 20)
        // Search: DEMO-TECNO-003
        // ────────────────────────────────────────────────────────────
        Receipt::updateOrCreate(
            ['phone_serial_number' => 'DEMO-TECNO-003'],
            $base([
                'receipt_number'    => 'MR-DEMO-2026-003',
                'customer_name'     => 'Fatima Usman',
                'customer_phone'    => '09063456789',
                'customer_email'    => null,
                'customer_address'  => '5 Ring Road, Kano',
                'customer_sex'      => 'female',
                'phone_name'        => 'Tecno Spark 20',
                'phone_color'       => 'Magic Skin Blue',
                'phone_serial_number' => 'DEMO-TECNO-003',
                'amount'            => 95000,
                'amount_in_words'   => 'Ninety-Five Thousand Naira',
                'resale_code'       => 'RESALE003',
                'resale_pin'        => '246810',
                'payment_gateway_status' => 'offline',
                'generated_at'      => now()->subDays(20),
            ])
        );

        $this->command->info('');
        $this->command->info('✅ Demo phones seeded successfully!');
        $this->command->table(
            ['Serial Number', 'Expected Result', 'Owner', 'Device'],
            [
                ['DEMO-IPHONE13-001', '✅ Case 1 — Verified',       'Chukwuemeka Obi (08031234567)', 'Black iPhone 13'],
                ['DEMO-SAMSUNG-002',  '🔴 Case 2 — MISSING ALERT',  'Aisha Bello (07052345678)',     'White Samsung Galaxy S23'],
                ['DEMO-TECNO-003',    '✅ Case 1 — Verified',       'Fatima Usman (09063456789)',    'Blue Tecno Spark 20'],
                ['<any other serial>', '🔵 Case 3 — Not Found',     '—',                            '—'],
            ]
        );
        $this->command->info('');
        $this->command->info('Receipt Portal logins:');
        $this->command->info('  iPhone  → Phone: 08031234567  PIN: 123456');
        $this->command->info('  Samsung → Phone: 07052345678  PIN: 654321');
        $this->command->info('  Tecno   → Phone: 09063456789  PIN: 246810');
    }
}
