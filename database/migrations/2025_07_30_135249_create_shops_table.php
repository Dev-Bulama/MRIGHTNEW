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
    Schema::create('shops', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('shop_name');
        $table->string('owner_full_name');
        $table->string('business_address');
        $table->string('business_phone_1');
        $table->string('business_phone_2')->nullable();
        $table->string('business_email');
        $table->string('country');
        $table->string('state');
        $table->string('local_government');
        $table->string('logo')->nullable();
        $table->text('terms_and_conditions')->nullable();
        $table->json('additional_info')->nullable();
        $table->string('status')->default('pending_approval');
        $table->boolean('approved')->default(false);
        $table->timestamp('approved_at')->nullable();
        $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        $table->integer('total_receipts_generated')->default(0);
        $table->decimal('total_commission_earned', 10, 2)->default(0);
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};