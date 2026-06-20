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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('user_type', ['shop_owner', 'admin', 'customer'])->default('shop_owner');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('avatar')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['user_type']);
            $table->index(['status']);
            $table->index(['verified_at']);
            $table->index(['email_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};