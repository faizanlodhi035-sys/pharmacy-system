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
        // 1. Update medicines table
        Schema::table('medicines', function (Blueprint $table) {
            if (!Schema::hasColumn('medicines', 'base_unit_id')) {
                $table->unsignedBigInteger('base_unit_id')->nullable()->after('dosage_unit');
            }
            if (!Schema::hasColumn('medicines', 'strength')) {
                $table->string('strength')->nullable()->after('brand');
            }
            if (!Schema::hasColumn('medicines', 'dosage_form')) {
                $table->string('dosage_form')->nullable()->after('strength');
            }
            if (!Schema::hasColumn('medicines', 'sku')) {
                $table->string('sku')->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('medicines', 'reorder_level')) {
                $table->integer('reorder_level')->default(10)->after('alert_quantity');
            }
            if (!Schema::hasColumn('medicines', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('reorder_level');
            }
            if (!Schema::hasColumn('medicines', 'status')) {
                $table->string('status')->default('active')->after('tax_rate');
            }
        });

        // 2. Update medicine_batches table
        Schema::table('medicine_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine_batches', 'manufacturing_date')) {
                $table->date('manufacturing_date')->nullable()->after('batch_number');
            }
            if (!Schema::hasColumn('medicine_batches', 'purchase_price_per_base_unit')) {
                $table->decimal('purchase_price_per_base_unit', 12, 2)->nullable()->after('purchase_price');
            }
            if (!Schema::hasColumn('medicine_batches', 'selling_price_per_base_unit')) {
                $table->decimal('selling_price_per_base_unit', 12, 2)->nullable()->after('selling_price');
            }
            if (!Schema::hasColumn('medicine_batches', 'status')) {
                $table->string('status')->default('active')->after('selling_price_per_base_unit');
            }
        });

        // 3. Update purchase_invoice_items table
        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoice_items', 'packaging_id')) {
                $table->unsignedBigInteger('packaging_id')->nullable()->after('batch_number');
            }
            if (!Schema::hasColumn('purchase_invoice_items', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('packaging_id');
            }
            if (!Schema::hasColumn('purchase_invoice_items', 'conversion_to_base')) {
                $table->decimal('conversion_to_base', 14, 4)->default(1)->after('unit');
            }
        });

        // 4. Update sale_items table
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'packaging_id')) {
                $table->unsignedBigInteger('packaging_id')->nullable()->after('batch_id');
            }
            if (!Schema::hasColumn('sale_items', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('packaging_id');
            }
            if (!Schema::hasColumn('sale_items', 'conversion_to_base')) {
                $table->decimal('conversion_to_base', 14, 4)->default(1)->after('unit');
            }
        });

        // 5. Update sales_return_items table
        Schema::table('sales_return_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_return_items', 'packaging_id')) {
                $table->unsignedBigInteger('packaging_id')->nullable()->after('batch_id');
            }
            if (!Schema::hasColumn('sales_return_items', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('packaging_id');
            }
            if (!Schema::hasColumn('sales_return_items', 'conversion_to_base')) {
                $table->decimal('conversion_to_base', 14, 4)->default(1)->after('unit');
            }
        });

        // 6. Update purchase_return_items table
        Schema::table('purchase_return_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_return_items', 'packaging_id')) {
                $table->unsignedBigInteger('packaging_id')->nullable()->after('batch_id');
            }
            if (!Schema::hasColumn('purchase_return_items', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('packaging_id');
            }
            if (!Schema::hasColumn('purchase_return_items', 'conversion_to_base')) {
                $table->decimal('conversion_to_base', 14, 4)->default(1)->after('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_return_items', function (Blueprint $table) {
            $table->dropColumn(['packaging_id', 'unit_id', 'conversion_to_base']);
        });

        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->dropColumn(['packaging_id', 'unit_id', 'conversion_to_base']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['packaging_id', 'unit_id', 'conversion_to_base']);
        });

        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->dropColumn(['packaging_id', 'unit_id', 'conversion_to_base']);
        });

        Schema::table('medicine_batches', function (Blueprint $table) {
            $table->dropColumn(['manufacturing_date', 'purchase_price_per_base_unit', 'selling_price_per_base_unit', 'status']);
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['base_unit_id', 'strength', 'dosage_form', 'sku', 'reorder_level', 'tax_rate', 'status']);
        });
    }
};
