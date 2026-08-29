@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-blue-600 font-semibold mb-1">
                <a href="/reports" class="hover:underline">Reports</a>
                <span>/</span>
                <span>Purchase Report</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">2. Purchase Report</h2>
            <p class="text-xs text-gray-500 mt-1">Supplier purchase transactions, costs, paid amounts and pending dues</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-xs shadow hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="/reports/purchases" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Time Period</label>
                <select name="filter" onchange="this.form.submit()" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                    <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ $filter == 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $filter == 'monthly' ? 'selected' : '' }}>This Month</option>
                    <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Supplier</label>
                <select name="supplier_id" onchange="this.form.submit()" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="w-full bg-slate-800 text-white py-2 px-3 rounded-lg text-xs font-bold hover:bg-slate-900 transition">
                    Filter
                </button>
                <a href="/reports/purchases" class="bg-gray-100 text-gray-600 py-2 px-3 rounded-lg text-xs font-bold hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Purchases Cost</p>
            <h3 class="text-2xl font-bold text-blue-600 mt-1">PKR {{ number_format($totalPurchasesAmount, 2) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Paid Amount</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">PKR {{ number_format($totalPaidAmount, 2) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Due / Outstanding</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">PKR {{ number_format($totalDueAmount, 2) }}</h3>
        </div>
    </div>

    <!-- Purchase Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Purchase Transactions</h3>
            <span class="text-xs text-gray-400 font-semibold">{{ $purchases->count() }} records found</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase border-b border-gray-100 font-semibold">
                    <tr>
                        <th class="p-3.5">Invoice #</th>
                        <th class="p-3.5">Supplier</th>
                        <th class="p-3.5">Purchase Date</th>
                        <th class="p-3.5 text-right">Total Amount</th>
                        <th class="p-3.5 text-right">Paid Amount</th>
                        <th class="p-3.5 text-right">Due Amount</th>
                        <th class="p-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchases as $purchase)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3.5 font-bold text-blue-600">
                            <a href="/purchases/{{ $purchase->id }}" class="hover:underline">{{ $purchase->invoice_number }}</a>
                        </td>
                        <td class="p-3.5 font-medium text-gray-800">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        <td class="p-3.5 text-gray-600">{{ $purchase->purchase_date }}</td>
                        <td class="p-3.5 text-right font-bold">PKR {{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="p-3.5 text-right text-emerald-600 font-semibold">PKR {{ number_format($purchase->paid_amount, 2) }}</td>
                        <td class="p-3.5 text-right text-red-600 font-semibold">PKR {{ number_format($purchase->due_amount, 2) }}</td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $purchase->due_amount == 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}
                            ">
                                {{ $purchase->due_amount == 0 ? 'Paid' : 'Partial/Unpaid' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">No purchase records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
