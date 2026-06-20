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
        // Add secondary_phone column
        if (!Schema::hasColumn('users', 'secondary_phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('secondary_phone')->nullable()->after('phone_number');
            });
        }

        // Add status column
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])
                      ->default('active')->after('user_type');
            });
        }

        // Add last_login_at column
        if (!Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            });
        }

        // Add last_login_ip column
        if (!Schema::hasColumn('users', 'last_login_ip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            });
        }

        // Add import_metadata column
        if (!Schema::hasColumn('users', 'import_metadata')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('import_metadata')->nullable()->after('remember_token');
            });
        }

        // Add account_activated_at column
        if (!Schema::hasColumn('users', 'account_activated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('account_activated_at')->nullable()->after('import_metadata');
            });
        }

        // Add activation_token column
        if (!Schema::hasColumn('users', 'activation_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('activation_token')->nullable()->after('account_activated_at');
            });
        }

        // Add indexes (wrapped in try-catch to handle existing indexes)
        $this->addIndexesSafely();
    }

    /**
     * Add indexes safely.
     */
    private function addIndexesSafely()
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['phone_number'], 'users_phone_number_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['secondary_phone'], 'users_secondary_phone_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['status'], 'users_status_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['user_type'], 'users_user_type_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['email', 'phone_number'], 'users_email_phone_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes first (if they exist)
        $this->dropIndexesSafely();

        // Drop columns in reverse order
        $columnsToCheck = [
            'activation_token',
            'account_activated_at',
            'import_metadata',
            'last_login_ip',
            'last_login_at',
            'secondary_phone'
            // Note: We don't drop 'status' as it might be used elsewhere
        ];

        foreach ($columnsToCheck as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /**
     * Drop indexes safely.
     */
    private function dropIndexesSafely()
    {
        $indexes = [
            'users_phone_number_index',
            'users_secondary_phone_index',
            'users_status_index',
            'users_user_type_index',
            'users_email_phone_index'
        ];

        foreach ($indexes as $index) {
            try {
                Schema::table('users', function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            } catch (\Exception $e) {
                // Index might not exist
            }
        }
    }
};