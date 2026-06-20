<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('user_roles', function (Blueprint $table) {
        $table->timestamp('assigned_at')->nullable()->change();
    });
}

public function down()
{
    Schema::table('user_roles', function (Blueprint $table) {
        $table->string('assigned_at')->nullable()->change();
    });
}
};
