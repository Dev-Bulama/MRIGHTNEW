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
        Schema::table('users', function (Blueprint $table) {
            // Add union-specific fields
            if (!Schema::hasColumn('users', 'assigned_states')) {
                $table->json('assigned_states')->nullable()->after('user_type')->comment('States assigned to union users');
            }
            
            if (!Schema::hasColumn('users', 'assigned_lgas')) {
                $table->json('assigned_lgas')->nullable()->after('assigned_states')->comment('LGAs assigned to union users');
            }
            
            if (!Schema::hasColumn('users', 'assigned_by')) {
                $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null')->after('assigned_lgas');
            }
            
            if (!Schema::hasColumn('users', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'assigned_at')) {
                $table->dropColumn('assigned_at');
            }
            if (Schema::hasColumn('users', 'assigned_by')) {
                $table->dropForeign(['assigned_by']);
                $table->dropColumn('assigned_by');
            }
            if (Schema::hasColumn('users', 'assigned_lgas')) {
                $table->dropColumn('assigned_lgas');
            }
            if (Schema::hasColumn('users', 'assigned_states')) {
                $table->dropColumn('assigned_states');
            }
        });
    }
};