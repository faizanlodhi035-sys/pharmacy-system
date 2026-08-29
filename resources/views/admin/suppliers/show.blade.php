@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Supplier Header Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $supplier->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fa-solid fa-user mr-1"></i> Contact: {{ $supplier->contact_person ?? 'N/A' }} | 
                <i class="fa-solid fa-phone ml-2 mr-1"></i> {{ $supplier->phone }} | 
                <i class="fa-solid fa-envelope ml-2 mr-1"></i> {{ $supplier->email ?? 'N/A' }}
            </p>
        </div>
        <div class="text-right">
            <span class="text-xs text-gray-500 block uppercase font-bold">Payable Balance</span>
            <span class="text-xl font-extrabold text-red-600">PKR {{ number_format($supplier->opening_balance, 2) }}</span>
        </div>
    </div>

    <!-- Purchase History Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Purchase History / Stock In</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Medicine Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Batch No</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Quantity</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Purchase Price</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($purchases as $purchase)
                        <tr>
                            <td class="px-4 py-3 text-gray-600">{{ $purchase->purchase_date }}</td>
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $purchase->medicine->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $purchase->batch_number }}</td>
                            <td class="px-4 py-3 font-semibold text-blue-600">{{ $purchase->quantity }}</td>
                            <td class="px-4 py-3 text-gray-600">PKR {{ number_format($purchase->purchase_price, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-gray-800">PKR {{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                No purchase history found for this supplier.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>

        <div class="mt-6">
            <a href="/suppliers" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-300">Back to Suppliers</a>
        </div>
    </div>
</div>
@endsection