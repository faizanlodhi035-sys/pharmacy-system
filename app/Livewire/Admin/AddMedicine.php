<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicinePackaging;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\PackagingService;
use App\Services\StockLedgerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class AddMedicine extends Component
{
    public string $product_type = 'medicine';
    public string $name = '';
    public string $generic_name = '';
    public string $brand = '';
    public string $strength = '';
    public string $dosage_form = '';
    public string $manufacturer = '';
    public string $barcode = '';
    public string $sku = '';
    public string $category_id = '';
    public string $category_search = '';
    public string $supplier_id = '';
    public string $batch_number = '';
    public string $quantity = '';
    public string $initial_stock_unit = 'base';
    public string $purchase_price = '';
    public string $selling_price = '';
    public string $expiry_date = '';
    public string $alert_quantity = '10';
    public string $reorder_level = '10';
    public string $tax_rate = '0';

    public bool $has_expiry = true;
    public bool $track_batches = true;

    // Unit Hierarchy Properties
    public string $primary_unit = '';
    public string $secondary_unit = '';
    public string $base_unit = 'Tablet';
    public string $dosage_unit = '';
    public string $primary_unit_to_secondary = '10';
    public string $secondary_unit_to_base = '10';

    public string $primary_unit_purchase_price = '';
    public string $secondary_unit_purchase_price = '';
    public string $base_unit_purchase_price = '';

    public string $primary_unit_selling_price = '';
    public string $secondary_unit_selling_price = '';
    public string $base_unit_selling_price = '';

    public string $primary_unit_barcode = '';
    public string $secondary_unit_barcode = '';
    public string $base_unit_barcode = '';

    public string $search = '';
    public string $productTypeFilter = 'all';
    public string $categoryFilter = '';
    public string $supplierFilter = '';
    public string $stockFilter = '';

    public function updatedCategoryId($val): void
    {
        if (!empty($val)) {
            $cat = Category::find($val);
            $this->category_search = $cat?->name ?? '';
        } else {
            $this->category_search = '';
        }
    }

    public function selectCategory(int $categoryId, ?string $name = null): void
    {
        $this->category_id = (string) $categoryId;
        if ($name) {
            $this->category_search = $name;
        } else {
            $cat = Category::find($categoryId);
            $this->category_search = $cat?->name ?? '';
        }
        $this->resetValidation('category_id');
    }

    public function clearCategory(): void
    {
        $this->category_id = '';
        $this->category_search = '';
    }

    public function quickCreateCategory(string $name): void
    {
        $name = trim($name);
        if (empty($name)) {
            return;
        }

        $slug = Str::slug($name);
        if (empty($slug)) {
            $slug = 'cat-' . time();
        }

        $cat = Category::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'product_type' => $this->product_type,
            ]
        );

        $this->category_id = (string) $cat->id;
        $this->category_search = $cat->name;
        $this->resetValidation('category_id');
    }

    public function updatedDosageForm(string $form): void
    {
        $formLower = strtolower(trim($form));
        if (empty($formLower)) {
            return;
        }

        if (
            str_contains($formLower, 'syrup') ||
            str_contains($formLower, 'suspension') ||
            str_contains($formLower, 'elixir') ||
            str_contains($formLower, 'solution') ||
            str_contains($formLower, 'drops') ||
            str_contains($formLower, 'lotion') ||
            str_contains($formLower, 'mouthwash') ||
            str_contains($formLower, 'liquid')
        ) {
            $this->base_unit = 'Bottle';
            $this->secondary_unit = '';
            $this->primary_unit = 'Box';
            $this->primary_unit_to_secondary = '12';
            $this->secondary_unit_to_base = '1';
        } elseif (
            str_contains($formLower, 'injection') ||
            str_contains($formLower, 'infusion') ||
            str_contains($formLower, 'vial') ||
            str_contains($formLower, 'ampoule')
        ) {
            $this->base_unit = str_contains($formLower, 'ampoule') ? 'Ampoule' : 'Vial';
            $this->secondary_unit = '';
            $this->primary_unit = 'Pack';
            $this->primary_unit_to_secondary = '5';
            $this->secondary_unit_to_base = '1';
        } elseif (
            str_contains($formLower, 'cream') ||
            str_contains($formLower, 'ointment') ||
            str_contains($formLower, 'gel')
        ) {
            $this->base_unit = 'Tube';
            $this->secondary_unit = '';
            $this->primary_unit = 'Box';
            $this->primary_unit_to_secondary = '10';
            $this->secondary_unit_to_base = '1';
        } elseif (
            str_contains($formLower, 'sachet') ||
            str_contains($formLower, 'powder')
        ) {
            $this->base_unit = 'Sachet';
            $this->secondary_unit = '';
            $this->primary_unit = 'Box';
            $this->primary_unit_to_secondary = '20';
            $this->secondary_unit_to_base = '1';
        } elseif (
            str_contains($formLower, 'inhaler') ||
            str_contains($formLower, 'spray')
        ) {
            $this->base_unit = 'Bottle';
            $this->secondary_unit = '';
            $this->primary_unit = 'Box';
        } elseif (str_contains($formLower, 'capsule')) {
            $this->base_unit = 'Capsule';
            $this->secondary_unit = 'Strip';
            $this->primary_unit = 'Pack';
            $this->primary_unit_to_secondary = '10';
            $this->secondary_unit_to_base = '10';
        } elseif (str_contains($formLower, 'tablet')) {
            $this->base_unit = 'Tablet';
            $this->secondary_unit = 'Strip';
            $this->primary_unit = 'Pack';
            $this->primary_unit_to_secondary = '10';
            $this->secondary_unit_to_base = '10';
        }
    }

    public function updatedInitialStockUnit(string $unit): void
    {
        if ($unit !== 'base' && $unit !== 'secondary' && $unit !== 'primary') {
            // User selected a specific unit name from dropdown, e.g. Bottle, Capsule, Tube, Vial
            $this->base_unit = $unit;
            $this->initial_stock_unit = 'base';
        }
    }

    public function updatedProductType(string $type): void
    {
        $this->category_id = '';
        $this->category_search = '';
        $this->barcode = '';
        if ($type === 'general') {
            $this->primary_unit = '';
            $this->secondary_unit = '';
            $this->base_unit = 'Piece';
            $this->primary_unit_to_secondary = '1';
            $this->secondary_unit_to_base = '1';
            $this->has_expiry = false;
            $this->track_batches = false;
            $this->dosage_unit = '';
            $this->generic_name = '';
            $this->manufacturer = '';
        } else {
            $this->primary_unit = '';
            $this->secondary_unit = '';
            $this->base_unit = 'Tablet';
            $this->primary_unit_to_secondary = '10';
            $this->secondary_unit_to_base = '10';
            $this->has_expiry = true;
            $this->track_batches = true;
        }
    }

    public function setProductType(string $type): void
    {
        $this->product_type = $type;
        $this->updatedProductType($type);
    }

    public function getCalculatedSecondaryConversionProperty(): int
    {
        return max(1, (int) ($this->secondary_unit_to_base ?: 1));
    }

    public function getCalculatedPrimaryConversionProperty(): int
    {
        $p2s = max(1, (int) ($this->primary_unit_to_secondary ?: 1));
        $s2b = max(1, (int) ($this->secondary_unit_to_base ?: 1));
        return $p2s * $s2b;
    }

    public function save(): void
    {
        $rules = [
            'product_type' => 'required|in:medicine,general',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:100',
            'dosage_form' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'alert_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'primary_unit' => 'nullable|string|max:50',
            'secondary_unit' => 'nullable|string|max:50',
            'base_unit' => 'required|string|max:50',
            'primary_unit_to_secondary' => 'nullable|integer|min:1',
            'secondary_unit_to_base' => 'nullable|integer|min:1',
            'primary_unit_purchase_price' => 'nullable|numeric|min:0',
            'secondary_unit_purchase_price' => 'nullable|numeric|min:0',
            'base_unit_purchase_price' => 'nullable|numeric|min:0',
            'primary_unit_selling_price' => 'nullable|numeric|min:0',
            'secondary_unit_selling_price' => 'nullable|numeric|min:0',
            'base_unit_selling_price' => 'nullable|numeric|min:0',
            'primary_unit_barcode' => 'nullable|string|max:255',
            'secondary_unit_barcode' => 'nullable|string|max:255',
            'base_unit_barcode' => 'nullable|string|max:255',
        ];

        if ($this->product_type === 'medicine') {
            $rules['generic_name'] = 'nullable|string|max:255';
            $rules['manufacturer'] = 'nullable|string|max:255';
            $rules['expiry_date'] = 'required|date';
            $rules['batch_number'] = 'required|string|max:255';
        } else {
            $rules['expiry_date'] = $this->has_expiry ? 'required|date' : 'nullable|date';
            $rules['batch_number'] = 'nullable|string|max:255';
        }

        $this->validate($rules);

        DB::transaction(function () {
            // 1. Ensure Base Unit exists in units table
            $baseUnitSlug = Str::slug($this->base_unit);
            $baseUnit = Unit::firstOrCreate(
                ['unit_id' => $baseUnitSlug],
                [
                    'name' => trim($this->base_unit),
                    'symbol' => substr(trim($this->base_unit), 0, 4),
                    'allow_decimal' => in_array($baseUnitSlug, ['ml', 'liter', 'gram', 'kg']),
                    'status' => 'active',
                ]
            );

            $basePrice = $this->base_unit_selling_price !== '' ? (float)$this->base_unit_selling_price : (float)$this->selling_price;
            $basePurchasePrice = $this->base_unit_purchase_price !== '' ? (float)$this->base_unit_purchase_price : (float)$this->purchase_price;

            $batchNum = trim($this->batch_number);
            if (empty($batchNum)) {
                $batchNum = 'BAT-' . strtoupper(Str::random(6));
            }

            $primaryToSecondary = max(1, (int) ($this->primary_unit_to_secondary ?: 1));
            $secondaryToBase = $this->product_type === 'medicine' ? max(1, (int) ($this->secondary_unit_to_base ?: 1)) : 1;

            $medicineData = [
                'category_id' => $this->category_id,
                'product_type' => $this->product_type,
                'generic_name' => $this->product_type === 'medicine' ? ($this->generic_name ?: null) : null,
                'brand' => $this->brand ?: null,
                'strength' => $this->strength ?: null,
                'dosage_form' => $this->dosage_form ?: null,
                'dosage_unit' => $this->product_type === 'medicine' ? ($this->dosage_unit ?: $this->base_unit) : $this->base_unit,
                'base_unit_id' => $baseUnit->id,
                'manufacturer' => $this->product_type === 'medicine' ? ($this->manufacturer ?: null) : null,
                'barcode' => $this->barcode ?: ($this->base_unit_barcode ?: null),
                'sku' => $this->sku ?: null,
                'alert_quantity' => (int) ($this->alert_quantity ?: 10),
                'reorder_level' => (int) ($this->reorder_level ?: ($this->alert_quantity ?: 10)),
                'tax_rate' => (float) ($this->tax_rate ?: 0),
                'has_expiry' => $this->has_expiry,
                'track_batches' => $this->track_batches,
                'primary_unit' => $this->primary_unit ?: null,
                'secondary_unit' => $this->product_type === 'medicine' ? ($this->secondary_unit ?: null) : null,
                'base_unit' => $this->base_unit,
                'primary_unit_to_secondary' => $primaryToSecondary,
                'secondary_unit_to_base' => $secondaryToBase,
                'unit_price' => $basePrice,
                'purchase_price' => $basePurchasePrice,
                'primary_unit_selling_price' => $this->primary_unit_selling_price !== '' ? (float)$this->primary_unit_selling_price : null,
                'secondary_unit_selling_price' => ($this->product_type === 'medicine' && $this->secondary_unit_selling_price !== '') ? (float)$this->secondary_unit_selling_price : null,
                'base_unit_selling_price' => $basePrice,
                'status' => 'active',
            ];

            $medicine = Medicine::firstOrCreate(
                ['name' => trim($this->name)],
                $medicineData
            );

            $medicine->update($medicineData);

            // 2. Set up medicine_packaging records (Hierarchy & Conversions)
            // A. Base Unit Packaging (Conversion = 1)
            $basePkg = MedicinePackaging::updateOrCreate(
                [
                    'medicine_id' => $medicine->id,
                    'unit_id' => $baseUnit->id,
                ],
                [
                    'conversion_to_base' => 1.0,
                    'quantity_in_parent' => 1.0,
                    'parent_packaging_id' => null,
                    'display_name' => $this->base_unit,
                    'barcode' => $this->base_unit_barcode ?: $this->barcode ?: null,
                    'purchase_price' => $basePurchasePrice,
                    'sale_price' => $basePrice,
                    'allow_purchase' => true,
                    'allow_sale' => true,
                    'status' => 'active',
                ]
            );

            $secondaryPkg = null;
            // B. Secondary Unit Packaging (if provided)
            if (!empty($this->secondary_unit) && $this->product_type === 'medicine') {
                $secUnitSlug = Str::slug($this->secondary_unit);
                $secUnit = Unit::firstOrCreate(
                    ['unit_id' => $secUnitSlug],
                    ['name' => trim($this->secondary_unit), 'symbol' => substr(trim($this->secondary_unit), 0, 4), 'allow_decimal' => false, 'status' => 'active']
                );

                $secConversion = (float) $secondaryToBase;
                $secSalePrice = $this->secondary_unit_selling_price !== '' ? (float)$this->secondary_unit_selling_price : round($basePrice * $secConversion, 2);
                $secPurchasePrice = $this->secondary_unit_purchase_price !== '' ? (float)$this->secondary_unit_purchase_price : round($basePurchasePrice * $secConversion, 2);

                $secondaryPkg = MedicinePackaging::updateOrCreate(
                    [
                        'medicine_id' => $medicine->id,
                        'unit_id' => $secUnit->id,
                    ],
                    [
                        'conversion_to_base' => $secConversion,
                        'quantity_in_parent' => (float)$secondaryToBase,
                        'parent_packaging_id' => $basePkg->id,
                        'display_name' => "{$this->secondary_unit} ({$secondaryToBase} {$this->base_unit}s)",
                        'barcode' => $this->secondary_unit_barcode ?: null,
                        'purchase_price' => $secPurchasePrice,
                        'sale_price' => $secSalePrice,
                        'allow_purchase' => true,
                        'allow_sale' => true,
                        'status' => 'active',
                    ]
                );
            }

            // C. Primary Unit Packaging (if provided)
            if (!empty($this->primary_unit)) {
                $primUnitSlug = Str::slug($this->primary_unit);
                $primUnit = Unit::firstOrCreate(
                    ['unit_id' => $primUnitSlug],
                    ['name' => trim($this->primary_unit), 'symbol' => substr(trim($this->primary_unit), 0, 4), 'allow_decimal' => false, 'status' => 'active']
                );

                $primConversion = (float) ($primaryToSecondary * $secondaryToBase);
                $primSalePrice = $this->primary_unit_selling_price !== '' ? (float)$this->primary_unit_selling_price : round($basePrice * $primConversion, 2);
                $primPurchasePrice = $this->primary_unit_purchase_price !== '' ? (float)$this->primary_unit_purchase_price : round($basePurchasePrice * $primConversion, 2);

                MedicinePackaging::updateOrCreate(
                    [
                        'medicine_id' => $medicine->id,
                        'unit_id' => $primUnit->id,
                    ],
                    [
                        'conversion_to_base' => $primConversion,
                        'quantity_in_parent' => (float)$primaryToSecondary,
                        'parent_packaging_id' => $secondaryPkg ? $secondaryPkg->id : $basePkg->id,
                        'display_name' => "{$this->primary_unit} ({$primConversion} {$this->base_unit}s)",
                        'barcode' => $this->primary_unit_barcode ?: null,
                        'purchase_price' => $primPurchasePrice,
                        'sale_price' => $primSalePrice,
                        'allow_purchase' => true,
                        'allow_sale' => true,
                        'status' => 'active',
                    ]
                );
            }

            // 3. Initial stock calculation: convert entered quantity to Base Units
            $inputQty = (float) $this->quantity;
            $selectedConversion = 1.0;
            $selectedUnitName = $this->base_unit;

            if ($this->initial_stock_unit === 'primary' && !empty($this->primary_unit)) {
                $selectedConversion = (float) ($primaryToSecondary * $secondaryToBase);
                $selectedUnitName = $this->primary_unit;
            } elseif ($this->initial_stock_unit === 'secondary' && !empty($this->secondary_unit)) {
                $selectedConversion = (float) $secondaryToBase;
                $selectedUnitName = $this->secondary_unit;
            }

            $initialBaseQuantity = round($inputQty * $selectedConversion, 4);

            // 4. Create initial batch in BASE UNITS
            $batch = MedicineBatch::create([
                'medicine_id' => $medicine->id,
                'supplier_id' => $this->supplier_id ?: null,
                'batch_number' => $batchNum,
                'quantity' => $initialBaseQuantity,
                'purchase_price' => $basePurchasePrice,
                'selling_price' => $basePrice,
                'purchase_price_per_base_unit' => $basePurchasePrice,
                'selling_price_per_base_unit' => $basePrice,
                'expiry_date' => $this->has_expiry ? ($this->expiry_date ?: null) : null,
                'status' => 'active',
            ]);

            // 5. Record OPENING_STOCK movement in stock ledger
            if ($initialBaseQuantity > 0) {
                app(StockLedgerService::class)->recordMovement(
                    medicineId: $medicine->id,
                    batchId: $batch->id,
                    type: 'OPENING_STOCK',
                    referenceId: null,
                    referenceType: null,
                    selectedUnitId: $baseUnit->id,
                    quantity: $inputQty,
                    conversionToBase: $selectedConversion,
                    baseQuantity: $initialBaseQuantity,
                    userId: auth()->id() ?? 1,
                    notes: "Initial opening stock entered as {$inputQty} {$selectedUnitName}"
                );
            } else {
                app(StockLedgerService::class)->syncInventory($medicine->id);
            }
        });

        $label = $this->product_type === 'general' ? 'General Store Item' : 'Medicine';
        session()->flash(
            'message',
            "{$label} Added Successfully with complete packaging hierarchy!"
        );

        $this->reset([
            'name',
            'generic_name',
            'brand',
            'strength',
            'dosage_form',
            'manufacturer',
            'barcode',
            'sku',
            'category_id',
            'category_search',
            'supplier_id',
            'batch_number',
            'quantity',
            'initial_stock_unit',
            'purchase_price',
            'selling_price',
            'expiry_date',
            'primary_unit',
            'secondary_unit',
            'base_unit',
            'dosage_unit',
            'primary_unit_to_secondary',
            'secondary_unit_to_base',
            'primary_unit_purchase_price',
            'secondary_unit_purchase_price',
            'base_unit_purchase_price',
            'primary_unit_selling_price',
            'secondary_unit_selling_price',
            'base_unit_selling_price',
            'primary_unit_barcode',
            'secondary_unit_barcode',
            'base_unit_barcode',
        ]);

        $this->alert_quantity = '10';
        $this->reorder_level = '10';
        $this->tax_rate = '0';
        if ($this->product_type === 'general') {
            $this->base_unit = 'Piece';
            $this->has_expiry = false;
            $this->track_batches = false;
            $this->primary_unit_to_secondary = '1';
            $this->secondary_unit_to_base = '1';
        } else {
            $this->base_unit = 'Tablet';
            $this->has_expiry = true;
            $this->track_batches = true;
            $this->primary_unit_to_secondary = '10';
            $this->secondary_unit_to_base = '10';
        }
    }

    public function render()
    {
        $query = Medicine::with([
            'category',
            'packagings.unit',
            'batches.supplier',
            'inventory',
        ]);

        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($this->productTypeFilter !== 'all') {
            $query->productType($this->productTypeFilter);
        }

        if ($this->categoryFilter !== '') {
            $query->where('category_id', $this->categoryFilter);
        }

        $medicines = $query->latest()->get();

        if ($this->supplierFilter !== '') {
            $medicines = $medicines->filter(function ($medicine) {
                return $medicine->batches->contains(function ($batch) {
                    return (string) $batch->supplier_id === (string) $this->supplierFilter;
                });
            });
        }

        if ($this->stockFilter !== '') {
            $medicines = $medicines->filter(function ($medicine) {
                $stock = $medicine->batches->sum('quantity');
                $alert = $medicine->reorder_level ?? $medicine->alert_quantity ?? 10;

                $expired = $medicine->batches
                    ->where('quantity', '>', 0)
                    ->contains(function ($batch) {
                        return $batch->expiry_date && $batch->expiry_date->isPast();
                    });

                return match ($this->stockFilter) {
                    'in_stock' => $stock > $alert && ! $expired,
                    'low_stock' => $stock > 0 && $stock <= $alert && ! $expired,
                    'out_of_stock' => $stock <= 0,
                    'expired' => $expired,
                    default => true,
                };
            });
        }

        $allMedicines = Medicine::with('batches')->get();
        $totalMedicines = $allMedicines->count();
        $totalStock = $allMedicines->sum(function ($medicine) {
            return $medicine->batches->sum('quantity');
        });

        $lowStock = $allMedicines->filter(function ($medicine) {
            $stock = $medicine->batches->sum('quantity');
            $alert = $medicine->reorder_level ?? $medicine->alert_quantity ?? 10;
            return $stock > 0 && $stock <= $alert;
        })->count();

        $expired = $allMedicines->filter(function ($medicine) {
            return $medicine->batches
                ->where('quantity', '>', 0)
                ->contains(function ($batch) {
                    return $batch->expiry_date && $batch->expiry_date->isPast();
                });
        })->count();

        $categories = Category::orderBy('name')->get();
        $formCategories = Category::forProductType($this->product_type)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $availableUnits = Unit::active()->orderBy('name')->get();

        // 1. Suggested Product Names (Tailored to Product Type)
        if ($this->product_type === 'general') {
            $standardProducts = [
                'Lux Beauty Soap 100g', 'Lux Soap Rose 140g', 'Dettol Soap Original 100g', 'Dettol Cool Soap 100g',
                'Lifebuoy Total Soap 100g', 'Safeguard Pure White 100g', 'Dove White Beauty Bar 100g', 'Palmolive Soap 100g',
                'Sunsilk Black Shine Shampoo 180ml', 'Sunsilk Soft & Smooth Shampoo 180ml', 'Head & Shoulders Classic Clean 180ml',
                'Pantene Pro-V Shampoo 180ml', 'Dove Intense Repair Shampoo 180ml', 'Lifebuoy Shampoo 175ml',
                'Colgate Maximum Cavity Protection 100g', 'Colgate Total 100g', 'Sensodyne Rapid Relief 100g',
                'Sensodyne Multi Care 100g', 'Close Up Deep Action 100g', 'Oral-B Toothbrush Medium',
                'Aquafina Mineral Water 500ml', 'Aquafina Mineral Water 1.5L', 'Nestle Pure Life 500ml', 'Nestle Pure Life 1.5L',
                'Pampers Active Baby Size 3 (56 Diapers)', 'Pampers Premium Protection Size 4', 'Huggies Extra Care Diapers Size 3',
                'Canbebe Baby Diapers Size 4', 'Johnson\'s Baby Shampoo 200ml', 'Johnson\'s Baby Oil 200ml', 'Johnson\'s Baby Powder 200g',
                'Johnson\'s Baby Lotion 200ml', 'Johnson\'s Baby Wipes (80 Pack)', 'Dettol Disinfectant Liquid 500ml',
                'Dettol Antiseptic Liquid 100ml', 'Vaseline Petroleum Jelly Original 100ml', 'Nivea Soft Moisturizing Cream 100ml',
                'Nivea Crème Tin 100ml', 'Gillette Mach 3 Razor', 'Gillette Blue 2 Plus (5 Pack)', 'Gillette Foamy Shaving Foam 200ml',
                'Lipton Yellow Label Tea 400g', 'Tapal Danedar Tea 400g', 'Everyday Tea Whitener 375g',
                'Olper\'s Milk 1 Liter', 'Nestle MilkPak 1 Liter', 'Red Bull Energy Drink 250ml', 'Sting Energy Drink 300ml',
                'Hand Sanitizer Gel 500ml', 'KN95 Protective Face Masks (10 Pack)', 'Surgical Face Masks 3-Ply (50 Pack)',
                'Crepe Bandage 4 Inch (1 Roll)', 'Cotton Wool Absorbent 100g', 'First Aid Adhesive Bandages (100 Strips)',
                'Pyodine Solution 60ml', 'Digital Thermometer'
            ];
            $dbProducts = Medicine::where('product_type', 'general')->pluck('name')->toArray();

            $standardBrands = [
                'Unilever', 'Procter & Gamble (P&G)', 'Nestle Pakistan', 'Reckitt Benckiser', 'Colgate-Palmolive',
                'Johnson & Johnson', 'Engro Foods', 'National Foods', 'Shan Foods', 'Tapal Tea',
                'Lipton', 'Dalda Foods', 'Hilal Confectionery', 'Shield Corporation', 'Canbebe / Ontex',
                'Kimberly-Clark (Huggies)', 'Pampers', 'Nivea (Beiersdorf)', 'L\'Oreal Pakistan',
                'Gillette', 'Dettol', 'Lux', 'Lifebuoy', 'Sunsilk', 'Safeguard', 'Head & Shoulders',
                'Dove', 'Vaseline', 'Coca-Cola Company', 'PepsiCo', 'Aquafina', 'Nestle Pure Life',
                'Mitchell\'s', 'Mitchell\'s Farms', 'English Biscuits (Peek Freans)',
                'Continental Biscuits (LU)', 'Knorr', 'Rafhan', 'Marico (Parachute)', 'Dabur'
            ];
            $dbBrands = Medicine::where('product_type', 'general')->whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->toArray();
        } else {
            $standardProducts = [
                'Panadol 500mg (Tablet)', 'Panadol Extra (Tablet)', 'Panadol CF (Tablet)', 'Panadol Children Syrup 120ml',
                'Disprin 300mg (Effervescent Tablet)', 'Disprin Direct (Tablet)', 'Brufen 400mg (Tablet)', 'Brufen 600mg (Tablet)',
                'Brufen DS Suspension 120ml', 'Augmentin 625mg (Tablet)', 'Augmentin 1g (Tablet)', 'Augmentin DS Syrup 156.25mg/5ml',
                'Flagyl 400mg (Tablet)', 'Flagyl 200mg/5ml Suspension 60ml', 'Arinac Forte (Tablet)', 'Arinac Syrup 60ml',
                'Flyxotic 500mg (Tablet)', 'Ciproxin 500mg (Tablet)', 'Risek 20mg (Capsule)', 'Risek 40mg (Capsule)', 'Risek 40mg (Insta Sachet)',
                'Kestine 10mg (Tablet)', 'Softin 10mg (Tablet)', 'Rigix 10mg (Tablet)', 'Zyrtec 10mg (Tablet)', 'Gravinate 50mg (Tablet)',
                'Buscopan 10mg (Tablet)', 'Buscopan Plus (Tablet)', 'Ponstan 500mg (Tablet)', 'Ponstan Forte (Tablet)',
                'CaC 1000 Plus Effervescent (10 Tablets)', 'Surbex Z (Tablet)', 'Neurobion (Tablet)', 'Neurobion Injection',
                'Entox-P (Tablet)', 'Hydryllin Syrup 120ml', 'Pulmonol Syrup 120ml', 'Acefyl Syrup 120ml', 'T-Day 5mg (Tablet)',
                'Polyfax Skin Ointment 20g', 'Polyfax Eye Ointment 6g', 'Fastum Gel 50g', 'Voltral Emulgel 1% 50g',
                'Calamox 625mg (Tablet)', 'Novidat 500mg (Tablet)', 'Leflox 500mg (Tablet)', 'Velosef 500mg (Capsule)',
                'Methycobal 500mcg (Tablet)', 'Gabbro 500mg (Tablet)', 'Lowplat 75mg (Tablet)', 'Ascard 75mg (Tablet)'
            ];
            $dbProducts = Medicine::where('product_type', 'medicine')->pluck('name')->toArray();

            $standardBrands = [
                'GSK (GlaxoSmithKline)', 'Abbott Laboratories', 'Getz Pharma', 'The Searle Company',
                'Sanofi-Aventis', 'Sami Pharmaceuticals', 'Hilton Pharma', 'Pfizer Pakistan', 'Novartis',
                'Bayer Pakistan', 'Ferozsons Laboratories', 'CCL Pharmaceuticals', 'Bosch Pharmaceuticals',
                'PharmEvo', 'Highnoon Laboratories', 'Platinum Pharmaceuticals', 'AGP Limited',
                'Martin Dow Marker', 'Barrett Hodgson', 'Macter International', 'Reckitt Benckiser Health',
                'Bio-Labs', 'Atco Laboratories', 'Zafa Pharmaceuticals', 'BPL', 'Remington Pharma'
            ];
            $dbBrands = Medicine::where('product_type', 'medicine')->whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->toArray();
        }

        $suggestedProductNames = collect(array_merge($standardProducts, $dbProducts))->unique()->sort()->values();
        $suggestedBrands = collect(array_merge($standardBrands, $dbBrands))->unique()->sort()->values();

        // 2. Suggested Generic Names
        $standardGenerics = [
            'Paracetamol', 'Ibuprofen', 'Amoxicillin', 'Amoxicillin + Clavulanic Acid (Co-Amoxiclav)',
            'Ciprofloxacin', 'Omeprazole', 'Esomeprazole', 'Azithromycin', 'Metformin HCl',
            'Cefixime', 'Cefradine', 'Diclofenac Sodium', 'Diclofenac Potassium',
            'Loratadine', 'Cetirizine HCl', 'Levocetirizine', 'Montelukast Sodium',
            'Metronidazole', 'Doxycycline', 'Fluconazole', 'Artemether + Lumefantrine',
            'Amlodipine', 'Losartan Potassium', 'Atorvastatin', 'Rosuvastatin',
            'Pantoprazole', 'Domperidone', 'Ondansetron', 'Tramadol HCl', 'Mefenamic Acid',
            'Chlorpheniramine Maleate', 'Dextromethorphan', 'Salbutamol', 'Budesonide',
            'Prednisolone', 'Hydrocortisone', 'Dexamethasone', 'Sitagliptin', 'Glimepiride',
            'Gliclazide', 'Clopidogrel', 'Aspirin (Acetylsalicylic Acid)', 'Bisoprolol Fumarate',
            'Valsartan', 'Spironolactone', 'Furosemide', 'Gatifloxacin', 'Moxifloxacin',
            'Levofloxacin', 'Clarithromycin', 'Tranexamic Acid', 'Iron Polymaltose + Folic Acid',
            'Calcium Carbonate + Vitamin D3', 'Cholecalciferol (Vitamin D3)', 'Ascorbic Acid (Vitamin C)',
            'Zinc Sulfate', 'Oral Rehydration Salts (ORS)'
        ];
        $dbGenerics = Medicine::whereNotNull('generic_name')->where('generic_name', '!=', '')->distinct()->pluck('generic_name')->toArray();
        $suggestedGenerics = collect(array_merge($standardGenerics, $dbGenerics))->unique()->sort()->values();

        // 3. Suggested Manufacturers
        $standardManufacturers = [
            'GlaxoSmithKline (GSK) Pakistan Ltd', 'Abbott Laboratories Pakistan Ltd',
            'Getz Pharma (Pvt) Ltd', 'The Searle Company Ltd', 'Sanofi-Aventis Pakistan Ltd',
            'Sami Pharmaceuticals (Pvt) Ltd', 'Hilton Pharma (Pvt) Ltd', 'Pfizer Pakistan Ltd',
            'Novartis Pharma Pakistan Ltd', 'Bayer Pakistan (Pvt) Ltd', 'Ferozsons Laboratories Ltd',
            'CCL Pharmaceuticals (Pvt) Ltd', 'Bosch Pharmaceuticals (Pvt) Ltd', 'PharmEvo (Pvt) Ltd',
            'Highnoon Laboratories Ltd', 'Platinum Pharmaceuticals', 'AGP Limited',
            'Martin Dow Marker Ltd', 'Barrett Hodgson Pakistan', 'Macter International Ltd',
            'Reckitt Benckiser Pakistan', 'Unilever Pakistan Ltd', 'Procter & Gamble Pakistan Ltd',
            'Nestle Pakistan Ltd', 'Colgate-Palmolive Pakistan Ltd', 'Engro Foods Pakistan'
        ];
        $dbManufacturers = Medicine::whereNotNull('manufacturer')->where('manufacturer', '!=', '')->distinct()->pluck('manufacturer')->toArray();
        $suggestedManufacturers = collect(array_merge($standardManufacturers, $dbManufacturers))->unique()->sort()->values();

        // 4. Suggested Dosage Forms & Strengths
        $suggestedDosageForms = [
            'Tablet', 'Film-Coated Tablet', 'Dispersible Tablet', 'Chewable Tablet',
            'Effervescent Tablet', 'Capsule', 'Softgel Capsule', 'Syrup', 'Suspension',
            'Oral Drops', 'Elixir', 'Injection (IV/IM)', 'Infusion', 'Cream', 'Ointment',
            'Gel', 'Lotion', 'Eye Drops', 'Ear Drops', 'Nasal Spray', 'Inhaler',
            'Nebulizer Solution', 'Suppository', 'Sachet / Powder', 'Mouthwash'
        ];

        $suggestedStrengths = [
            '500mg', '250mg', '125mg', '62.5mg', '1000mg (1g)', '100mg', '200mg', '400mg',
            '50mg', '25mg', '20mg', '10mg', '5mg', '2.5mg', '1mg', '0.5mg', '20mg/ml',
            '10mg/5ml', '100mg/5ml', '125mg/5ml', '200mg/5ml', '250mg/5ml', '400mg/5ml',
            '10%', '5%', '2%', '1%', '0.1%', '0.05%'
        ];

        return view('livewire.admin.add-medicine', [
            'medicines' => $medicines,
            'categories' => $categories,
            'formCategories' => $formCategories,
            'suppliers' => $suppliers,
            'availableUnits' => $availableUnits,
            'totalMedicines' => $totalMedicines,
            'totalStock' => $totalStock,
            'lowStock' => $lowStock,
            'expired' => $expired,
            'suggestedProductNames' => $suggestedProductNames,
            'suggestedBrands' => $suggestedBrands,
            'suggestedGenerics' => $suggestedGenerics,
            'suggestedManufacturers' => $suggestedManufacturers,
            'suggestedDosageForms' => $suggestedDosageForms,
            'suggestedStrengths' => $suggestedStrengths,
        ])->layout('layouts.app');
    }
}