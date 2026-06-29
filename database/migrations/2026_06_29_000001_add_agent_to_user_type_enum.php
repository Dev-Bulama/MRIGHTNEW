<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the user_type ENUM to include 'agent'
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin','shop_owner','agent','customer','union','union_executive','user') NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin','shop_owner','customer','union','union_executive','user') NOT NULL DEFAULT 'customer'");
    }
};
