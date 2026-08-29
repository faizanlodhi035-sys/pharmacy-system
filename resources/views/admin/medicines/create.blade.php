@extends('layouts.app')

@section('content')
<div class="space-y-6 p-6">
    
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 text-green-700 text-sm rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 text-red-700 text-sm rounded shadow-sm">
            <p class="font-bold">Please fix the following errors:</p>
            <ul class="list-disc pl-5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ADD MEDICINE FORM CARD -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Add New Medicine & Batch</h2>
        
        <form action="/medicines" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" class="w-full mt-1 p-2 border rounded-lg bg-white focus:ring focus:ring-blue-300 text-sm" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Medicine Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Medicine Name</label>
                    <input type="text" name="name" class="w-full mt-1 p-2 border rounded-lg focus:ring focus:ring-blue-300 text-sm" placeholder="e.g. Panadol 500mg" autocomplete="off" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Batch Number</label>
                    <input type="text" name="batch_number" class="w-full mt-1 p-2 border rounded-lg text-sm" placeholder="e.g. BATCH-001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Initial Quantity</label>
                    <input type="number" name="quantity" class="w-full mt-1 p-2 border rounded-lg text-sm" placeholder="e.g. 100" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Selling Price (PKR)</label>
                    <input type="number" name="selling_price" class="w-full mt-1 p-2 border rounded-lg text-sm" placeholder="e.g. 50" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input type="date" name="expiry_date" class="w-full mt-1 p-2 border rounded-lg text-sm" required>
                </div>
                <div class="flex items-end justify-end">
                    <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 shadow">Save Medicine</button>
                </div>
            </div>
        </form>
    </div>

    <!-- MEDICINES LIST TABLE CARD -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Medicines Inventory List</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Medicine Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Category</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Batch Details</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Total Stock</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Selling Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($medicines as $medicine)
                        <tr>
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $medicine->name }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-semibold">
                                    {{ $medicine->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                @foreach($medicine->batches as $batch)
                                    <div>Batch: <span class="font-semibold">{{ $batch->batch_number }}</span> (Exp: {{ $batch->expiry_date }})</div>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $medicine->batches->sum('quantity') }} units
                            </td>
                            <td class="px-4 py-3 font-bold text-blue-600">
                                @foreach($medicine->batches as $batch)
                                    <div>PKR {{ $batch->selling_price }}</div>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                No medicines found in inventory.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection