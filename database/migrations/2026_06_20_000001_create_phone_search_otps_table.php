<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('phone_search_otps', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number');
            $table->string('seller_whatsapp');
            $table->string('otp', 6);
            $table->string('session_token')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('verified')->default(false);
            $table->boolean('used')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index(['session_token', 'used']);
            $table->index(['seller_whatsapp', 'expires_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('phone_search_otps'); }
};
