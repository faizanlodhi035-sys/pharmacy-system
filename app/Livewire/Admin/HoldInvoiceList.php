<?php

namespace App\Livewire\Admin;

use App\Models\HoldInvoice;
use Livewire\Component;
use Livewire\WithPagination;

class HoldInvoiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'held';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function restoreToPos(int $id)
    {
        return redirect()->to('/pos?restore_hold=' . $id);
    }

    public function deleteHold(int $id): void
    {
        $hold = HoldInvoice::find($id);
        if ($hold) {
            $hold->delete();
            session()->flash('message', "Held Invoice {$hold->reference_number} deleted successfully.");
        }
    }

    public function render()
    {
        $query = HoldInvoice::query();

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $holdInvoices = $query->latest()->paginate(10);

        $totalHeldCount = HoldInvoice::where('status', 'held')->count();
        $totalHeldAmount = HoldInvoice::where('status', 'held')->sum('total_amount');
        $totalRestoredCount = HoldInvoice::where('status', 'restored')->count();

        return view('livewire.admin.hold-invoice-list', [
            'holdInvoices' => $holdInvoices,
            'totalHeldCount' => $totalHeldCount,
            'totalHeldAmount' => $totalHeldAmount,
            'totalRestoredCount' => $totalRestoredCount,
        ])->layout('layouts.app');
    }
}
