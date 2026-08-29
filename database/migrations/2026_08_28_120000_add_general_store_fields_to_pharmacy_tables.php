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
        // 1. Add General Store fields to Medicines Table
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('product_type')->default('medicine')->after('category_id');
            $table->boolean('has_expiry')->default(true)->after('barcode');
            $table->boolean('track_batches')->default(true)->after('has_expiry');
            $table->string('dosage_unit')->nullable()->change();
        });

        // 2. Add Product Type to Categories Table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('product_type')->default('both')->after('description');
        });

        // 3. Make Expiry Date Nullable in Medicine Batches Table
        Schema::table('medicine_batches', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->change();
        });

        // 4. Make Expiry Date Nullable in Purchase Invoice Items Table
        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->date('expiry_date')->nullable(false)->change();
        });

        Schema::table('medicine_batches', function (Blueprint $table) {
            $table->date('expiry_date')->nullable(false)->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['product_type', 'has_expiry', 'track_batches']);
            $table->string('dosage_unit')->nullable(false)->change();
        });
    }
};
