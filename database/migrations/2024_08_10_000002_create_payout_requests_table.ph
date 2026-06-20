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
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            
            // User relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Request details
            $table->string('request_number')->unique();
            $table->decimal('amount_requested', 10, 2);
            $table->decimal('amount_approved', 10, 2)->nullable();
            $table->decimal('commission_balance', 10, 2); // Balance at time of request
            
            // Request information
            $table->text('reason');
            
            // Bank details
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            
            // Status and workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid', 'cancelled'])
                  ->default('pending');
            
            // Admin actions
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('payment_reference')->nullable();
            
            // Approval tracking
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Rejection tracking
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            
            // Payment tracking
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('request_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
    }
};