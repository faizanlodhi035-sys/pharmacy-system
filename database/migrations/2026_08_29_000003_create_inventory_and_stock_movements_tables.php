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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->unique()->constrained('medicines')->cascadeOnDelete();
            $table->decimal('total_base_quantity', 14, 4)->default(0);
            $table->decimal('reserved_base_quantity', 14, 4)->default(0);
            $table->decimal('available_base_quantity', 14, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('type'); // PURCHASE, SALE, PURCHASE_RETURN, SALE_RETURN, ADJUSTMENT_IN, ADJUSTMENT_OUT, DAMAGE, EXPIRED, OPENING_STOCK
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('selected_unit_id')->nullable();
            $table->decimal('quantity', 14, 4)->default(0);
            $table->decimal('conversion_to_base', 14, 4)->default(1);
            $table->decimal('base_quantity', 14, 4)->default(0);
            $table->decimal('previous_stock', 14, 4)->default(0);
            $table->decimal('new_stock', 14, 4)->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('batch_id')->references('id')->on('medicine_batches')->nullOnDelete();
            $table->foreign('selected_unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory');
    }
};
