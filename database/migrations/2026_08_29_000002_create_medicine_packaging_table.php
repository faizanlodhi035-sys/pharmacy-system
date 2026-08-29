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
        Schema::create('medicine_packaging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_packaging_id')->nullable();
            $table->decimal('quantity_in_parent', 12, 4)->default(1);
            $table->decimal('conversion_to_base', 14, 4)->default(1);
            $table->string('display_name')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->boolean('allow_purchase')->default(true);
            $table->boolean('allow_sale')->default(true);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('parent_packaging_id')->references('id')->on('medicine_packaging')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_packaging');
    }
};
