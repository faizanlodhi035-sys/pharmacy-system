<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\MedicineBatch;
use Illuminate\Support\Facades\DB;

class PurchaseCreate extends Component
{
    public $suppliers, $medicines;
    public $supplier_id, $invoice_number, $purchase_date;
    
    // Item form fields
    public $medicine_id, $batch_number, $quantity = 1, $purchase_price, $selling_price, $expiry_date, $tax_percent = 0;
    
    public $cart = [];

    public function mount()
    {
        $this->suppliers = Supplier::all();
        $this->medicines = Medicine::all();
        $this->purchase_date = date('Y-m-d');
        $this->invoice_number = 'PINV-' . rand(10000, 99999);
    }

    public function addItem()
    {
        $this->validate([
            'medicine_id' => 'required',
            'batch_number' => 'required',
            'quantity' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $medicine = Medicine::find($this->medicine_id);

        $itemTotal = $this->quantity * $this->purchase_price;
        $taxAmount = $itemTotal * ($this->tax_percent / 100);
        $finalTotal = $itemTotal + $taxAmount;

        $this->cart[] = [
            'medicine_id' => $this->medicine_id,
            'medicine_name' => $medicine->name,
            'batch_number' => $this->batch_number,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'expiry_date' => $this->expiry_date,
            'tax_percent' => $this->tax_percent,
            'total' => $finalTotal
        ];

        // Reset item fields
        $this->reset(['medicine_id', 'batch_number', 'quantity', 'purchase_price', 'selling_price', 'expiry_date', 'tax_percent']);
        $this->quantity = 1;
        $this->tax_percent = 0;
    }

    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function($item) {
            return $item['quantity'] * $item['purchase_price'];
        });
    }

    public function getTaxTotalProperty()
    {
        return collect($this->cart)->sum(function($item) {
            $itemTotal = $item['quantity'] * $item['purchase_price'];
            return $itemTotal * ($item['tax_percent'] / 100);
        });
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal + $this->taxTotal;
    }

    public function saveInvoice()
    {
        $this->validate([
            'supplier_id' => 'required',
            'invoice_number' => 'required',
            'purchase_date' => 'required|date',
        ]);

        if (empty($this->cart)) {
            session()->flash('error', 'Please add at least one medicine item to the invoice.');
            return;
        }

        DB::transaction(function () {
            // 1. Create Purchase Invoice
            $invoice = PurchaseInvoice::create([
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->invoice_number,
                'purchase_date' => $this->purchase_date,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxTotal,
                'grand_total' => $this->grandTotal,
            ]);

            // 2. Update Supplier Payable Balance (Udhar increase hoga)
            $supplier = Supplier::find($this->supplier_id);
            $supplier->increment('opening_balance', $this->grandTotal);

            // 3. Process Items & Automatic Stock Update
            foreach ($this->cart as $item) {
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'medicine_id' => $item['medicine_id'],
                    'batch_number' => $item['batch_number'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'expiry_date' => $item['expiry_date'],
                    'tax_percent' => $item['tax_percent'],
                    'total' => $item['total']
                ]);

                // Automatic Stock Update in Medicine Batches Table
                MedicineBatch::create([
                    'medicine_id' => $item['medicine_id'],
                    'batch_number' => $item['batch_number'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'expiry_date' => $item['expiry_date'],
                ]);
            }
        });

        session()->flash('message', 'Purchase Invoice Created & Stock Updated Successfully!');
        return redirect()->to('/purchases');
    }

    public function render()
    {
        return view('livewire.admin.purchase-create');
    }
}