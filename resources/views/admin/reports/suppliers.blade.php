@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-blue-600 font-semibold mb-1">
                <a href="/reports" class="hover:underline">Reports</a>
                <span>/</span>
                <span>Supplier Report</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">7. Supplier Report</h2>
            <p class="text-xs text-gray-500 mt-1">Supplier purchase transactions, payment history and outstanding balance ledgers</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-xs shadow hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Active Suppliers</p>
            <h3 class="text-2xl font-bold text-cyan-600 mt-1">{{ number_format($totalSuppliersCount) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Grand Purchase Volume</p>
            <h3 class="text-2xl font-bold text-blue-600 mt-1">PKR {{ number_format($grandPurchasesValue, 2) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Grand Total Payable Dues</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">PKR {{ number_format($grandTotalPayableDue, 2) }}</h3>
        </div>
    </div>

    <!-- Supplier Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Supplier Accounts & Purchase Ledgers</h3>
            <span class="text-xs text-gray-400 font-semibold">{{ $supplierStats->count() }} suppliers</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase border-b border-gray-100 font-semibold">
                    <tr>
                        <th class="p-3.5">Supplier Name</th>
                        <th class="p-3.5">Contact Details</th>
                        <th class="p-3.5 text-center">Purchases Count</th>
                        <th class="p-3.5 text-right">Total Purchased</th>
                        <th class="p-3.5 text-right">Total Paid</th>
                        <th class="p-3.5 text-right">Pending Balance Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($supplierStats as $supplier)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3.5 font-bold text-gray-800">
                            {{ $supplier->name }}
                            <span class="block text-[10px] text-gray-400 font-normal">Contact: {{ $supplier->contact_person ?? 'N/A' }}</span>
                        </td>
                        <td class="p-3.5 text-gray-600">
                            {{ $supplier->phone }}
                            <span class="block text-[10px] text-gray-400">{{ $supplier->email ?? '' }}</span>
                        </td>
                        <td class="p-3.5 text-center font-bold text-blue-600">{{ number_format($supplier->total_purchases_count) }}</td>
                        <td class="p-3.5 text-right font-bold text-gray-800">PKR {{ number_format($supplier->total_purchased_amount, 2) }}</td>
                        <td class="p-3.5 text-right font-bold text-emerald-600">PKR {{ number_format($supplier->total_paid_amount, 2) }}</td>
                        <td class="p-3.5 text-right font-extrabold {{ $supplier->pending_due_amount > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            PKR {{ number_format($supplier->pending_due_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">No supplier data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
