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
        Schema::create('purchase_invoice_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('purchase_invoice_id')->constrained()->onDelete('cascade');
        $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
        $table->string('batch_number');
        $table->integer('quantity');
        $table->decimal('purchase_price', 10, 2);
        $table->decimal('selling_price', 10, 2);
        $table->date('expiry_date');
        $table->decimal('tax_percent', 5, 2)->default(0);
        $table->decimal('total', 12, 2);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_items');
    }
};
