@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sales History & Invoices</h1>
            <p class="text-sm text-gray-500">View all past transactions, customer details, and print thermal receipts.</p>
        </div>
        <div>
            <a href="/pos" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 shadow">
                <i class="fa-solid fa-cash-register mr-1"></i> Open POS
            </a>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Invoice #</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Date & Time</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Subtotal</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Total Amount</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Paid Amount</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($sales as $sale)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-blue-600">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-3 text-gray-700">PKR {{ number_format($sale->subtotal, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-gray-900">PKR {{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-green-600 font-semibold">PKR {{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="/sales/{{ $sale->id }}" target="_blank" class="bg-slate-800 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-slate-900 shadow">
                                    <i class="fa-solid fa-print mr-1"></i> Print Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                No sales invoices found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection