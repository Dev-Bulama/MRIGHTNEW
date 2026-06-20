<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('receipts', 'is_missing')) {
                $table->boolean('is_missing')->default(false)->after('status');
            }
            if (!Schema::hasColumn('receipts', 'missing_reported_at')) {
                $table->timestamp('missing_reported_at')->nullable()->after('is_missing');
            }
            if (!Schema::hasColumn('receipts', 'missing_notes')) {
                $table->text('missing_notes')->nullable()->after('missing_reported_at');
            }
            if (!Schema::hasColumn('receipts', 'resale_pin')) {
                $table->string('resale_pin', 6)->nullable()->after('resale_code');
            }
        });
    }
    public function down(): void {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['is_missing', 'missing_reported_at', 'missing_notes', 'resale_pin']);
        });
    }
};
