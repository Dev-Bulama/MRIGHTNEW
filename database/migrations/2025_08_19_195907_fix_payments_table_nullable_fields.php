<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Make customer fields nullable and add defaults
            $table->string('customer_email')->nullable()->default(null)->change();
            $table->string('customer_phone')->nullable()->default(null)->change();
            $table->string('payment_method')->nullable()->default('paystack')->change();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('customer_email')->nullable(false)->change();
            $table->string('customer_phone')->nullable(false)->change();
            $table->string('payment_method')->nullable(false)->change();
        });
    }
};