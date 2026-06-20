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
        Schema::table('payments', function (Blueprint $table) {
            // Add Paystack-specific columns
            if (!Schema::hasColumn('payments', 'paystack_reference')) {
                $table->string('paystack_reference')->nullable()->after('reference');
            }
            
            if (!Schema::hasColumn('payments', 'authorization_url')) {
                $table->text('authorization_url')->nullable()->after('paystack_reference');
            }
            
            if (!Schema::hasColumn('payments', 'access_code')) {
                $table->string('access_code')->nullable()->after('authorization_url');
            }
            
            if (!Schema::hasColumn('payments', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('currency');
            }
            
            if (!Schema::hasColumn('payments', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_email');
            }
            
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->default('paystack')->after('customer_phone');
            }
            
            if (!Schema::hasColumn('payments', 'gateway_response')) {
                $table->text('gateway_response')->nullable()->after('access_code');
            }
            
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('gateway_response');
            }
            
            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('transaction_id');
            }
            
            if (!Schema::hasColumn('payments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('paid_at');
            }
            
            // Make existing fields nullable if they aren't
            $table->text('metadata')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'paystack_reference',
                'authorization_url', 
                'access_code',
                'customer_email',
                'customer_phone',
                'payment_method',
                'gateway_response',
                'transaction_id',
                'paid_at',
                'verified_at'
            ]);
        });
    }
};