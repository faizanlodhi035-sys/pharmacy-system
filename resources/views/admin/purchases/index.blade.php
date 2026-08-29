@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Banner (Similar to Medicines Page) -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Management</h1>
            <p class="text-sm text-gray-500 mt-1">View and manage all supplier purchase invoices, tax details, and stock entries.</p>
        </div>
        <div>
            <a href="/purchases/create" class="bg-green-600 text-white px-4 py-2.5 rounded-lg font-bold text-sm hover:bg-green-700 shadow flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Create Purchase</span>
            </a>
        </div>
    </div>

    @if(session('message'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 text-sm font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Purchases Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Invoice No</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Supplier Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Purchase Date</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Subtotal</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Tax</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Grand Total</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($purchases as $purchase)
                        <tr>
                            <td class="px-4 py-3 font-bold text-blue-600">{{ $purchase->invoice_number }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $purchase->purchase_date }}</td>
                            <td class="px-4 py-3 text-gray-600">PKR {{ number_format($purchase->subtotal, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600">PKR {{ number_format($purchase->tax_amount, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-gray-900">PKR {{ number_format($purchase->grand_total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="/purchases/{{ $purchase->id }}" class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-blue-700 shadow">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                No purchase invoices found. Click "Create Purchase" to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection