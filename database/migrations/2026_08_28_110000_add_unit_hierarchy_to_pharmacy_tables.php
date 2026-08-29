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
        // 1. Add Unit Hierarchy to Medicines Table
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('primary_unit')->nullable()->after('dosage_unit');
            $table->string('secondary_unit')->nullable()->after('primary_unit');
            $table->string('base_unit')->default('Tablet')->after('secondary_unit');
            
            $table->integer('primary_unit_to_secondary')->default(1)->after('base_unit');
            $table->integer('secondary_unit_to_base')->default(1)->after('primary_unit_to_secondary');

            $table->decimal('primary_unit_selling_price', 10, 2)->nullable()->after('unit_price');
            $table->decimal('secondary_unit_selling_price', 10, 2)->nullable()->after('primary_unit_selling_price');
            $table->decimal('base_unit_selling_price', 10, 2)->nullable()->after('secondary_unit_selling_price');
        });

        // 2. Add Purchase Unit & Base Quantity to Purchase Invoice Items Table
        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->string('unit')->default('base')->after('batch_number');
            $table->decimal('base_quantity', 12, 2)->default(0)->after('quantity');
        });

        // 3. Add Sale Unit & Base Quantity to Sale Items Table
        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('unit')->default('base')->after('batch_id');
            $table->decimal('base_quantity', 12, 2)->default(0)->after('quantity');
        });

        // 4. Add Unit & Base Quantity to Sales Return Items Table
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->string('unit')->default('base')->after('batch_id');
            $table->decimal('base_quantity', 12, 2)->default(0)->after('quantity');
        });

        // 5. Add Unit & Base Quantity to Purchase Return Items Table
        Schema::table('purchase_return_items', function (Blueprint $table) {
            $table->string('unit')->default('base')->after('batch_id');
            $table->decimal('base_quantity', 12, 2)->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_return_items', function (Blueprint $table) {
            $table->dropColumn(['unit', 'base_quantity']);
        });

        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->dropColumn(['unit', 'base_quantity']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit', 'base_quantity']);
        });

        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->dropColumn(['unit', 'base_quantity']);
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn([
                'primary_unit',
                'secondary_unit',
                'base_unit',
                'primary_unit_to_secondary',
                'secondary_unit_to_base',
                'primary_unit_selling_price',
                'secondary_unit_selling_price',
                'base_unit_selling_price',
            ]);
        });
    }
};
