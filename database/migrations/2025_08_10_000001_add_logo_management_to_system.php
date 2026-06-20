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
        // Add logo field to users table for admin logo uploads
        Schema::table('users', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('user_type');
        });

        // Create system_logos table for AMPAT and MRight logos
        Schema::create('system_logos', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // 'ampat', 'mright'
            $table->string('logo_path');
            $table->string('original_name');
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->index(['type']);
        });

        // Create logo_uploads directory if it doesn't exist
        // This will be handled by the storage system
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
        
        Schema::dropIfExists('system_logos');
    }
};