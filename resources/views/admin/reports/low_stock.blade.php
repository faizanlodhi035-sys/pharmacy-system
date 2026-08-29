@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-blue-600 font-semibold mb-1">
                <a href="/reports" class="hover:underline">Reports</a>
                <span>/</span>
                <span>Low Stock Report</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">9. Low Stock Report</h2>
            <p class="text-xs text-gray-500 mt-1">Inventory stock alerts, out-of-stock items & reorder recommendations</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="/purchases/create" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold text-xs shadow hover:bg-emerald-700 transition flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Create Purchase Order</span>
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-xs shadow hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Alert List</span>
            </button>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-red-500">
            <p class="text-xs font-semibold text-gray-400 uppercase">Out of Stock Medicines</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">{{ number_format($outOfStockCount) }}</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Zero stock remaining</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500">
            <p class="text-xs font-semibold text-gray-400 uppercase">Low Stock Alerts</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($lowStockCount) }}</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Stock below alert threshold</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Alert Items</p>
            <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($lowStockMedicines->count()) }}</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Requires immediate restock</p>
        </div>
    </div>

    <!-- Low Stock Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Low Stock & Out of Stock Inventory</h3>
            <span class="text-xs text-gray-400 font-semibold">{{ $lowStockMedicines->count() }} alert items</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase border-b border-gray-100 font-semibold">
                    <tr>
                        <th class="p-3.5">Medicine Name</th>
                        <th class="p-3.5">Category</th>
                        <th class="p-3.5 text-center">Alert Limit</th>
                        <th class="p-3.5 text-center">Current Stock</th>
                        <th class="p-3.5 text-center">Stock Deficit</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lowStockMedicines as $medicine)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3.5 font-bold text-gray-800">
                            {{ $medicine->name }}
                            <span class="block text-[10px] text-gray-400 font-normal">{{ $medicine->generic_name ?? 'N/A' }}</span>
                        </td>
                        <td class="p-3.5 text-gray-600 font-medium">{{ $medicine->category->name ?? 'Uncategorized' }}</td>
                        <td class="p-3.5 text-center font-semibold text-gray-600">{{ $medicine->alert_quantity }}</td>
                        <td class="p-3.5 text-center font-extrabold {{ $medicine->current_stock == 0 ? 'text-red-600' : 'text-amber-600' }}">
                            {{ number_format($medicine->current_stock) }}
                        </td>
                        <td class="p-3.5 text-center font-bold text-red-500">- {{ number_format($medicine->stock_deficit) }} units</td>
                        <td class="p-3.5">
                            @if($medicine->current_stock == 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Out of Stock</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Low Stock Alert</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <a href="/purchases/create" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-2.5 py-1 rounded-md text-[11px] font-bold transition">
                                Reorder
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">All inventory stock levels are healthy! No low stock alerts.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
