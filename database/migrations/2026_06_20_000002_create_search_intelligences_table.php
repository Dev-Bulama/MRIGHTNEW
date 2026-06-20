<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('search_intelligences', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->string('seller_whatsapp')->nullable();
            $table->string('search_result')->nullable(); // found_not_missing, found_missing, not_found
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->string('timezone')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('isp')->nullable();
            $table->string('network_type')->nullable();
            $table->text('referrer')->nullable();
            $table->json('browser_fingerprint')->nullable();
            $table->string('session_id')->nullable();
            $table->string('image_path')->nullable(); // camera capture if granted
            $table->boolean('admin_notified')->default(false);
            $table->timestamps();
            $table->index('serial_number');
            $table->index('search_result');
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('search_intelligences'); }
};
