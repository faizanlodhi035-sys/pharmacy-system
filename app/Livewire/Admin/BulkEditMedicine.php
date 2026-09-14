<?php

namespace App\Livewire\Admin;

use App\Models\Medicine;
use Livewire\Component;
use Livewire\WithPagination;

class BulkEditMedicine extends Component
{
    use WithPagination;

    public array $rows = [];
    public string $search = '';

    public function mount()
    {
        $this->loadMedicines();
    }

    public function loadMedicines()
    {
        $medicines = Medicine::query()
            ->when($this->search, function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%");
            })
            ->limit(50)
            ->get();
            
        $this->rows = [];
        foreach ($medicines as $medicine) {
            $this->rows[$medicine->id] = [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'generic_name' => $medicine->generic_name ?? '',
                'brand' => $medicine->brand ?? '',
                'manufacturer' => $medicine->manufacturer ?? '',
                'dosage_unit' => $medicine->dosage_unit,
                'unit_price' => $medicine->unit_price,
                'purchase_price' => $medicine->purchase_price,
                'alert_quantity' => $medicine->alert_quantity,
                'barcode' => $medicine->barcode ?? '',
            ];
        }
    }

    public function updatedSearch()
    {
        $this->loadMedicines();
    }

    public function saveAll()
    {
        $this->resetErrorBag();
        
        $hasErrors = false;
        
        foreach ($this->rows as $id => $data) {
            if (empty(trim($data['name']))) {
                $this->addError("rows.{$id}.name", 'Name is required');
                $hasErrors = true;
                continue;
            }
            if (!is_numeric($data['unit_price'])) {
                $this->addError("rows.{$id}.unit_price", 'Must be a number');
                $hasErrors = true;
            }
            if (!is_numeric($data['purchase_price'])) {
                $this->addError("rows.{$id}.purchase_price", 'Must be a number');
                $hasErrors = true;
            }
        }
        
        if ($hasErrors) {
            $this->addError('general', 'Please fix the errors before saving.');
            return;
        }

        foreach ($this->rows as $id => $data) {
            Medicine::where('id', $id)->update([
                'name' => $data['name'],
                'generic_name' => $data['generic_name'],
                'brand' => $data['brand'],
                'manufacturer' => $data['manufacturer'],
                'dosage_unit' => $data['dosage_unit'],
                'unit_price' => $data['unit_price'],
                'purchase_price' => $data['purchase_price'],
                'alert_quantity' => $data['alert_quantity'],
                'barcode' => empty($data['barcode']) ? null : $data['barcode'],
            ]);
        }
        
        session()->flash('message', 'Medicines updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.bulk-edit-medicine');
    }
}
