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
        Schema::create('pre_approvals', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number');
            
            // Location Information
            $table->string('state');
            $table->string('local_government');
            
            // Shop Information (Optional)
            $table->string('shop_name')->nullable();
            $table->text('business_address')->nullable();
            
            // Pre-Approval System
            $table->string('approval_code')->unique();
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            
            // Relationships
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Additional Info
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['status', 'expires_at']);
            $table->index(['state', 'local_government']);
            $table->index(['email', 'phone_number']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_approvals');
    }
};