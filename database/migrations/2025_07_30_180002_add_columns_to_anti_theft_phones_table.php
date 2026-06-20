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
        Schema::table('anti_theft_phones', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('anti_theft_phones', 'serial_number')) {
                $table->string('serial_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'phone_model')) {
                $table->string('phone_model')->after('serial_number');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'phone_brand')) {
                $table->string('phone_brand')->after('phone_model');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'phone_color')) {
                $table->string('phone_color')->after('phone_brand');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'status')) {
                $table->string('status')->default('awaiting_ownership_verification')->after('phone_color');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'current_receipt_id')) {
                $table->foreignId('current_receipt_id')->nullable()->constrained('receipts')->after('status');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'current_owner_name')) {
                $table->string('current_owner_name')->nullable()->after('current_receipt_id');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'current_owner_phone')) {
                $table->string('current_owner_phone')->nullable()->after('current_owner_name');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'external_api_id')) {
                $table->string('external_api_id')->nullable()->after('current_owner_phone');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'last_api_sync')) {
                $table->timestamp('last_api_sync')->nullable()->after('external_api_id');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'api_response_data')) {
                $table->json('api_response_data')->nullable()->after('last_api_sync');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'status_history')) {
                $table->json('status_history')->nullable()->after('api_response_data');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'notes')) {
                $table->text('notes')->nullable()->after('status_history');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'registered_at')) {
                $table->timestamp('registered_at')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('anti_theft_phones', 'last_verified_at')) {
                $table->timestamp('last_verified_at')->nullable()->after('registered_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anti_theft_phones', function (Blueprint $table) {
            // Drop columns in reverse order
            $columns = [
                'last_verified_at', 'registered_at', 'notes', 'status_history',
                'api_response_data', 'last_api_sync', 'external_api_id',
                'current_owner_phone', 'current_owner_name', 'current_receipt_id',
                'status', 'phone_color', 'phone_brand', 'phone_model', 'serial_number'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('anti_theft_phones', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};