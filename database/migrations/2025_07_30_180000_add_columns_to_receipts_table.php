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
        Schema::table('receipts', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('receipts', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('receipts', 'shop_id')) {
                $table->foreignId('shop_id')->constrained()->onDelete('cascade')->after('user_id');
            }
            if (!Schema::hasColumn('receipts', 'receipt_number')) {
                $table->string('receipt_number')->unique()->after('shop_id');
            }
            if (!Schema::hasColumn('receipts', 'customer_name')) {
                $table->string('customer_name')->after('receipt_number');
            }
            if (!Schema::hasColumn('receipts', 'customer_phone')) {
                $table->string('customer_phone')->after('customer_name');
            }
            if (!Schema::hasColumn('receipts', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('receipts', 'customer_address')) {
                $table->string('customer_address')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('receipts', 'customer_sex')) {
                $table->string('customer_sex')->nullable()->after('customer_address');
            }
            if (!Schema::hasColumn('receipts', 'phone_name')) {
                $table->string('phone_name')->after('customer_sex');
            }
            if (!Schema::hasColumn('receipts', 'phone_color')) {
                $table->string('phone_color')->after('phone_name');
            }
            if (!Schema::hasColumn('receipts', 'phone_serial_number')) {
                $table->string('phone_serial_number')->after('phone_color');
            }
            if (!Schema::hasColumn('receipts', 'phone_serial_confirmation')) {
                $table->string('phone_serial_confirmation')->after('phone_serial_number');
            }
            if (!Schema::hasColumn('receipts', 'amount')) {
                $table->decimal('amount', 10, 2)->after('phone_serial_confirmation');
            }
            if (!Schema::hasColumn('receipts', 'amount_in_words')) {
                $table->string('amount_in_words')->after('amount');
            }
            if (!Schema::hasColumn('receipts', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('amount_in_words');
            }
            if (!Schema::hasColumn('receipts', 'resale_code')) {
                $table->string('resale_code')->after('payment_status');
            }
            if (!Schema::hasColumn('receipts', 'resale_code_confirmation')) {
                $table->string('resale_code_confirmation')->after('resale_code');
            }
            if (!Schema::hasColumn('receipts', 'enable_antitheft')) {
                $table->boolean('enable_antitheft')->default(false)->after('resale_code_confirmation');
            }
            if (!Schema::hasColumn('receipts', 'receipt_type')) {
                $table->string('receipt_type')->default('new_phone')->after('enable_antitheft');
            }
            if (!Schema::hasColumn('receipts', 'parent_receipt_id')) {
                $table->foreignId('parent_receipt_id')->nullable()->constrained('receipts')->after('receipt_type');
            }
            if (!Schema::hasColumn('receipts', 'status')) {
                $table->string('status')->default('active')->after('parent_receipt_id');
            }
            if (!Schema::hasColumn('receipts', 'service_fee')) {
                $table->decimal('service_fee', 10, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('receipts', 'payment_gateway_status')) {
                $table->string('payment_gateway_status')->nullable()->after('service_fee');
            }
            if (!Schema::hasColumn('receipts', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_gateway_status');
            }
            if (!Schema::hasColumn('receipts', 'payment_confirmed_at')) {
                $table->timestamp('payment_confirmed_at')->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('receipts', 'notes')) {
                $table->text('notes')->nullable()->after('payment_confirmed_at');
            }
            if (!Schema::hasColumn('receipts', 'metadata')) {
                $table->json('metadata')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('receipts', 'generated_at')) {
                $table->timestamp('generated_at')->nullable()->after('metadata');
            }
            if (!Schema::hasColumn('receipts', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Drop columns in reverse order
            $columns = [
                'deleted_at', 'generated_at', 'metadata', 'notes', 'payment_confirmed_at',
                'payment_reference', 'payment_gateway_status', 'service_fee', 'status',
                'parent_receipt_id', 'receipt_type', 'enable_antitheft', 'resale_code_confirmation',
                'resale_code', 'payment_status', 'amount_in_words', 'amount',
                'phone_serial_confirmation', 'phone_serial_number', 'phone_color', 'phone_name',
                'customer_sex', 'customer_address', 'customer_email', 'customer_phone',
                'customer_name', 'receipt_number', 'shop_id', 'user_id'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('receipts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};