<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Add union-related fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->json('assigned_states')->nullable()->after('user_type'); // States assigned to union
            $table->json('assigned_lgas')->nullable()->after('assigned_states'); // LGAs assigned to union
            $table->unsignedBigInteger('assigned_by')->nullable()->after('assigned_lgas'); // Admin who assigned
            $table->timestamp('assigned_at')->nullable()->after('assigned_by');
            
            $table->foreign('assigned_by')->references('id')->on('users');
        });

        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->json('permissions'); // Array of permissions
            $table->boolean('is_system_role')->default(false); // Prevent deletion
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });

        // Create user_roles pivot table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users');
            
            $table->unique(['user_id', 'role_id']);
        });

        // Create payout_requests table for shop owners
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->unsignedBigInteger('user_id'); // Shop owner
            $table->unsignedBigInteger('shop_id');
            $table->decimal('amount_requested', 10, 2);
            $table->decimal('amount_approved', 10, 2)->nullable();
            $table->decimal('commission_balance', 10, 2); // Shop's available balance
            $table->string('status')->default('pending'); // pending, approved, rejected, paid
            $table->text('reason')->nullable(); // Reason for request
            $table->text('admin_notes')->nullable(); // Admin notes
            $table->text('rejection_reason')->nullable();
            
            // Bank details
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            
            // Processing info
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('rejected_by')->references('id')->on('users');
            $table->foreign('paid_by')->references('id')->on('users');

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropColumn(['assigned_states', 'assigned_lgas', 'assigned_by', 'assigned_at']);
        });
    }
};