@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Banner (Matched with Suppliers Management style) -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Purchase Invoice</h1>
            <p class="text-sm text-gray-500 mt-1">Record supplier purchases, manage batch details, and auto-update inventory stock.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/purchases" class="bg-slate-800 text-white px-4 py-2.5 rounded-lg font-bold text-sm hover:bg-slate-900 shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-list-ul"></i>
                <span>Purchase History</span>
            </a>
        </div>
    </div>

    <!-- Purchase Form Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <form action="/purchases" method="POST" class="space-y-6">
            @csrf
            
            <!-- Supplier & Invoice Details Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Select Supplier *</label>
                    <select name="supplier_id" class="w-full p-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                        <option value="">Choose Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Invoice Number *</label>
                    <input type="text" name="invoice_number" value="PINV-{{ rand(1000,9999) }}" class="w-full p-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Purchase Date *</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="w-full p-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                </div>
            </div>

            <hr class="border-gray-100 my-4">

            <!-- Medicine Item Section -->
            <div>
                <h3 class="text-base font-bold text-gray-800 mb-3 flex items-center space-x-2">
                    <i class="fa-solid fa-capsules text-blue-600"></i>
                    <span>Add Medicine Item Details</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-3 bg-gray-50/70 p-4 rounded-xl border border-gray-100 items-end">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Medicine *</label>
                        <select name="items[0][medicine_id]" class="w-full p-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                            <option value="">Select Medicine</option>
                            @foreach($medicines as $med)
                                <option value="{{ $med->id }}">{{ $med->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Batch No *</label>
                        <input type="text" name="items[0][batch_number]" placeholder="BATCH-01" class="w-full p-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Quantity *</label>
                        <input type="number" name="items[0][quantity]" value="1" min="1" class="w-full p-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Purchase Rate *</label>
                        <input type="number" step="0.01" name="items[0][purchase_price]" placeholder="0.00" class="w-full p-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Selling Rate *</label>
                        <input type="number" step="0.01" name="items[0][selling_price]" placeholder="0.00" class="w-full p-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Expiry *</label>
                        <input type="date" name="items[0][expiry_date]" class="w-full p-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-blue-600" required>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="/purchases" class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-gray-300 transition">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold text-sm hover:bg-blue-700 shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Save & Update Stock</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection