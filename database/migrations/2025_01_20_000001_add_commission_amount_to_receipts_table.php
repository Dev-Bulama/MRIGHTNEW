<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Add commission_amount column
            if (!Schema::hasColumn('receipts', 'commission_amount')) {
                $table->decimal('commission_amount', 10, 2)->default(0)->after('service_fee');
            }
            
            // Add commission_rate column for flexibility
            if (!Schema::hasColumn('receipts', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(0)->comment('Commission percentage')->after('commission_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            if (Schema::hasColumn('receipts', 'commission_amount')) {
                $table->dropColumn('commission_amount');
            }
            if (Schema::hasColumn('receipts', 'commission_rate')) {
                $table->dropColumn('commission_rate');
            }
        });
    }
};