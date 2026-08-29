<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;

class MedicineList extends Component
{
    public function render()
    {
        $medicines = Medicine::with('category', 'batches')->get();

        return view('livewire.medicine-list', [
            'medicines' => $medicines
        ]);
    }
}