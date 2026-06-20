<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add missing columns to existing payments table.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Check and add missing columns only if they don't exist
            
            if (!Schema::hasColumn('payments', 'gateway')) {
                $table->string('gateway')->default('paystack')->after('payment_method');
            }
            
            if (!Schema::hasColumn('payments', 'description')) {
                $table->text('description')->nullable()->after('customer_phone');
            }
            
            if (!Schema::hasColumn('payments', 'paystack_access_code')) {
                $table->string('paystack_access_code')->nullable()->after('paystack_reference');
            }
            
            if (!Schema::hasColumn('payments', 'paystack_response')) {
                $table->json('paystack_response')->nullable()->after('paystack_access_code');
            }
            
            if (!Schema::hasColumn('payments', 'authorization_code')) {
                $table->string('authorization_code')->nullable()->after('paystack_response');
            }
            
            if (!Schema::hasColumn('payments', 'card_type')) {
                $table->string('card_type')->nullable()->after('authorization_code');
            }
            
            if (!Schema::hasColumn('payments', 'last4')) {
                $table->string('last4')->nullable()->after('card_type');
            }
            
            if (!Schema::hasColumn('payments', 'exp_month')) {
                $table->string('exp_month')->nullable()->after('last4');
            }
            
            if (!Schema::hasColumn('payments', 'exp_year')) {
                $table->string('exp_year')->nullable()->after('exp_month');
            }
            
            if (!Schema::hasColumn('payments', 'bank')) {
                $table->string('bank')->nullable()->after('exp_year');
            }
            
            if (!Schema::hasColumn('payments', 'initiated_at')) {
                $table->timestamp('initiated_at')->nullable()->after('metadata');
            }
            
            if (!Schema::hasColumn('payments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('initiated_at');
            }
            
            if (!Schema::hasColumn('payments', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('completed_at');
            }
            
            if (!Schema::hasColumn('payments', 'failure_reason')) {
                $table->text('failure_reason')->nullable()->after('failed_at');
            }
            
            if (!Schema::hasColumn('payments', 'refund_amount')) {
                $table->decimal('refund_amount', 15, 2)->nullable()->after('failure_reason');
            }
            
            if (!Schema::hasColumn('payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            }
            
            if (!Schema::hasColumn('payments', 'refund_reason')) {
                $table->text('refund_reason')->nullable()->after('refunded_at');
            }
            
            // Make sure existing nullable fields are actually nullable
            if (Schema::hasColumn('payments', 'customer_email')) {
                $table->string('customer_email')->nullable()->change();
            }
            
            if (Schema::hasColumn('payments', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->change();
            }
            
            if (Schema::hasColumn('payments', 'paystack_reference')) {
                $table->string('paystack_reference')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove columns that were added
            $columnsToRemove = [
                'gateway', 'description', 'paystack_access_code', 'paystack_response',
                'authorization_code', 'card_type', 'last4', 'exp_month', 'exp_year',
                'bank', 'initiated_at', 'completed_at', 'failed_at', 'failure_reason',
                'refund_amount', 'refunded_at', 'refund_reason'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};