<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_id')->unique(); // e.g. tablet, strip, pack, box, bottle, vial, ml
            $table->string('name');              // e.g. Tablet, Strip, Pack, Box, Bottle, Milliliter
            $table->string('symbol', 20)->nullable(); // e.g. Tab, Str, Pk, Bx, Btl, ml
            $table->boolean('allow_decimal')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Seed standard units
        $defaultUnits = [
            ['unit_id' => 'tablet', 'name' => 'Tablet', 'symbol' => 'Tab', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'capsule', 'name' => 'Capsule', 'symbol' => 'Cap', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'strip', 'name' => 'Strip', 'symbol' => 'Str', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'pack', 'name' => 'Pack', 'symbol' => 'Pk', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'box', 'name' => 'Box', 'symbol' => 'Bx', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'carton', 'name' => 'Carton', 'symbol' => 'Ctn', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'bottle', 'name' => 'Bottle', 'symbol' => 'Btl', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'vial', 'name' => 'Vial', 'symbol' => 'Vial', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'ampoule', 'name' => 'Ampoule', 'symbol' => 'Amp', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'tube', 'name' => 'Tube', 'symbol' => 'Tb', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'sachet', 'name' => 'Sachet', 'symbol' => 'Sach', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'piece', 'name' => 'Piece', 'symbol' => 'Pc', 'allow_decimal' => false, 'status' => 'active'],
            ['unit_id' => 'ml', 'name' => 'Milliliter', 'symbol' => 'ml', 'allow_decimal' => true, 'status' => 'active'],
            ['unit_id' => 'liter', 'name' => 'Liter', 'symbol' => 'L', 'allow_decimal' => true, 'status' => 'active'],
            ['unit_id' => 'mg', 'name' => 'Milligram', 'symbol' => 'mg', 'allow_decimal' => true, 'status' => 'active'],
            ['unit_id' => 'gram', 'name' => 'Gram', 'symbol' => 'g', 'allow_decimal' => true, 'status' => 'active'],
            ['unit_id' => 'drop', 'name' => 'Drop', 'symbol' => 'Drp', 'allow_decimal' => false, 'status' => 'active'],
        ];

        $now = now();
        foreach ($defaultUnits as &$u) {
            $u['created_at'] = $now;
            $u['updated_at'] = $now;
        }

        DB::table('units')->insert($defaultUnits);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
