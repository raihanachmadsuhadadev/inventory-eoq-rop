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
        Schema::create('purchase_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('hub_id')->nullable()->constrained('hubs')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('eoq_calculation_id')->nullable()->constrained('eoq_calculations')->nullOnDelete();
            $table->foreignId('rop_calculation_id')->nullable()->constrained('rop_calculations')->nullOnDelete();
            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedInteger('rop_value')->default(0);
            $table->unsignedInteger('recommended_quantity')->default(0);
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_recommendations');
    }
};
