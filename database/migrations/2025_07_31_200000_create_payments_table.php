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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('receipt_id')->constrained()->onDelete('cascade');
            
            // Payment reference and identification
            $table->string('reference')->unique();
            $table->string('gateway_reference')->nullable()->unique();
            
            // Payment amounts
            $table->decimal('amount', 12, 2);
            $table->decimal('service_fee', 8, 2)->default(0);
            $table->string('currency', 3)->default('NGN');
            
            // Payment method and status
            $table->enum('payment_method', ['paystack', 'bank_transfer', 'cash', 'pos'])->default('paystack');
            $table->enum('status', ['pending', 'successful', 'failed', 'cancelled', 'refunded'])->default('pending');
            
            // Customer information
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            
            // Gateway response and metadata
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            
            // Timestamps for payment lifecycle
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Additional notes
            $table->text('notes')->nullable();
            
            // Standard timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes(); // This adds the deleted_at column
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['receipt_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['currency', 'status']);
            $table->index('created_at');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};