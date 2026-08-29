@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-blue-600 font-semibold mb-1">
                <a href="/reports" class="hover:underline">Reports</a>
                <span>/</span>
                <span>Stock Report</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">3. Stock Report</h2>
            <p class="text-xs text-gray-500 mt-1">Inventory stock quantities, purchase cost valuation & potential sales profit</p>
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
        <form method="GET" action="/reports/stock" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                <select name="category_id" onchange="this.form.submit()" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-slate-800 text-white py-2 px-4 rounded-lg text-xs font-bold hover:bg-slate-900 transition">Filter</button>
                <a href="/reports/stock" class="bg-gray-100 text-gray-600 py-2 px-4 rounded-lg text-xs font-bold hover:bg-gray-200 transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Inventory Units</p>
            <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalInventoryQty) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Purchase Cost Valuation</p>
            <h3 class="text-2xl font-bold text-blue-600 mt-1">PKR {{ number_format($totalCostValuation, 2) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Retail Selling Valuation</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">PKR {{ number_format($totalRetailValuation, 2) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Potential Retail Profit</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">PKR {{ number_format($totalPotentialProfit, 2) }}</h3>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Medicine Stock Valuation Details</h3>
            <span class="text-xs text-gray-400 font-semibold">{{ $medicines->count() }} medicines</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase border-b border-gray-100 font-semibold">
                    <tr>
                        <th class="p-3.5">Medicine Name</th>
                        <th class="p-3.5">Category</th>
                        <th class="p-3.5 text-center">In Stock Qty</th>
                        <th class="p-3.5 text-right">Purchase Price</th>
                        <th class="p-3.5 text-right">Selling Price</th>
                        <th class="p-3.5 text-right">Cost Valuation</th>
                        <th class="p-3.5 text-right">Retail Valuation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($medicines as $medicine)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3.5 font-bold text-gray-800">
                            {{ $medicine->name }}
                            <span class="block text-[10px] text-gray-400 font-normal">{{ $medicine->generic_name ?? 'N/A' }}</span>
                        </td>
                        <td class="p-3.5 text-gray-600 font-medium">{{ $medicine->category->name ?? 'Uncategorized' }}</td>
                        <td class="p-3.5 text-center font-bold {{ $medicine->total_qty == 0 ? 'text-red-600' : 'text-blue-600' }}">
                            {{ number_format($medicine->total_qty) }}
                        </td>
                        <td class="p-3.5 text-right text-gray-600">PKR {{ number_format($medicine->purchase_price, 2) }}</td>
                        <td class="p-3.5 text-right text-gray-800 font-semibold">PKR {{ number_format($medicine->unit_price, 2) }}</td>
                        <td class="p-3.5 text-right text-blue-600 font-medium">PKR {{ number_format($medicine->cost_valuation, 2) }}</td>
                        <td class="p-3.5 text-right text-emerald-600 font-bold">PKR {{ number_format($medicine->retail_valuation, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">No stock data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
