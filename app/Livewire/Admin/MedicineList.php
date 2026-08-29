<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Supplier;
use Livewire\Component;

class MedicineList extends Component
{
    public string $search = '';
    public string $productTypeFilter = 'all';
    public string $categoryFilter = '';
    public string $stockFilter = '';

    public function render()
    {
        $query = Medicine::with(['category', 'batches', 'packagings.unit', 'inventory'])
            ->latest();

        if ($this->productTypeFilter !== 'all') {
            $query->productType($this->productTypeFilter);
        }

        // Search
        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('generic_name', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        // Category filter
        if ($this->categoryFilter !== '') {
            $query->where('category_id', $this->categoryFilter);
        }

        $medicines = $query->get();

        $categories = Category::orderBy('name')->get();
	$suppliers = Supplier::orderBy('name')->get();

        // Statistics
        $totalMedicines = Medicine::count();

        $allMedicines = Medicine::with('batches')->get();

        $totalStock = $allMedicines->sum(
            fn ($medicine) => $medicine->total_stock
        );

        $lowStock = $allMedicines->filter(
            fn ($medicine) =>
                $medicine->total_stock <= ($medicine->alert_quantity ?? 10)
        )->count();

        $expired = Medicine::whereHas('batches', function ($query) {
            $query->whereDate('expiry_date', '<', now()->toDateString())
                ->where('quantity', '>', 0);
        })->count();

        // Stock filter
        if ($this->stockFilter !== '') {
            $medicines = $medicines->filter(function ($medicine) {

                $stock = $medicine->total_stock;
                $alert = $medicine->alert_quantity ?? 10;

                return match ($this->stockFilter) {
                    'in_stock' => $stock > $alert,

                    'low_stock' => $stock > 0 && $stock <= $alert,

                    'out_of_stock' => $stock <= 0,

                    'expired' => $medicine->batches
                        ->where('quantity', '>', 0)
                        ->contains(
                            fn ($batch) =>
                                $batch->expiry_date &&
                                $batch->expiry_date->isPast()
                        ),

                    default => true,
                };
            });
        }

        return view('livewire.admin.medicine-list', [
    'medicines' => $medicines,
    'categories' => $categories,
    'suppliers' => $suppliers,
    'totalMedicines' => $totalMedicines,
    'totalStock' => $totalStock,
    'lowStock' => $lowStock,
    'expired' => $expired,
])->layout('layouts.app'); 
    }
}