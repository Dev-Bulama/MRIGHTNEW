<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fix user_type enum to include 'union'
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('shop_owner', 'admin', 'customer', 'union') DEFAULT 'shop_owner'");
        
        // 2. Add union fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'assigned_states')) {
                $table->json('assigned_states')->nullable()->after('user_type');
            }
            if (!Schema::hasColumn('users', 'assigned_lgas')) {
                $table->json('assigned_lgas')->nullable()->after('assigned_states');
            }
            if (!Schema::hasColumn('users', 'assigned_by')) {
                $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null')->after('assigned_lgas');
            }
            if (!Schema::hasColumn('users', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_by');
            }
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
        });

        // 3. Add commission fields to receipts table
        Schema::table('receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('receipts', 'commission_amount')) {
                $table->decimal('commission_amount', 10, 2)->default(0)->after('service_fee');
            }
            if (!Schema::hasColumn('receipts', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(0)->after('commission_amount');
            }
        });
    }

    public function down(): void
    {
        // Revert changes
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('shop_owner', 'admin', 'customer') DEFAULT 'shop_owner'");
        
        Schema::table('users', function (Blueprint $table) {
            $columns = ['assigned_at', 'assigned_by', 'assigned_lgas', 'assigned_states', 'last_name', 'first_name'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    if ($column === 'assigned_by') {
                        $table->dropForeign(['assigned_by']);
                    }
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (Schema::hasColumn('receipts', 'commission_rate')) {
                $table->dropColumn('commission_rate');
            }
            if (Schema::hasColumn('receipts', 'commission_amount')) {
                $table->dropColumn('commission_amount');
            }
        });
    }
};