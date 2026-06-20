<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_approved_users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->string('secondary_phone')->nullable();
            $table->enum('user_type', ['customer', 'shop_owner'])->default('shop_owner');
            $table->string('shop_name')->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('state')->nullable();
            $table->string('local_government')->nullable();
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            $table->timestamp('used_at')->nullable();
            $table->bigInteger('used_by_user_id')->nullable();
            $table->json('import_metadata')->nullable();
            $table->timestamps();
            
            $table->index(['email', 'phone_number']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_approved_users');
    }
};