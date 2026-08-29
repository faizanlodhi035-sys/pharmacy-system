@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-blue-600 font-semibold mb-1">
                <a href="/reports" class="hover:underline">Reports</a>
                <span>/</span>
                <span>Best Selling Products</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">8. Best Selling Report</h2>
            <p class="text-xs text-gray-500 mt-1">Top performing medicines ranked by sales volume and revenue generation</p>
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
        <form method="GET" action="/reports/best-selling" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Time Period</label>
                <select name="period" onchange="this.form.submit()" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                    <option value="all" {{ $period == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today Only</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Top Limit</label>
                <select name="limit" onchange="this.form.submit()" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>Top 10 Products</option>
                    <option value="25" {{ $limit == 25 ? 'selected' : '' }}>Top 25 Products</option>
                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>Top 50 Products</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-slate-800 text-white py-2 px-4 rounded-lg text-xs font-bold hover:bg-slate-900 transition">Filter</button>
                <a href="/reports/best-selling" class="bg-gray-100 text-gray-600 py-2 px-4 rounded-lg text-xs font-bold hover:bg-gray-200 transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Top Items Total Sold Units</p>
            <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($overallQuantitySold) }} units</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Top Items Total Revenue</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">PKR {{ number_format($overallRevenueGenerated, 2) }}</h3>
        </div>
    </div>

    <!-- Best Selling Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Best Selling Medicines Leaderboard</h3>
            <span class="text-xs text-gray-400 font-semibold">Top {{ $bestSellers->count() }} items</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase border-b border-gray-100 font-semibold">
                    <tr>
                        <th class="p-3.5 text-center">Rank</th>
                        <th class="p-3.5">Medicine Name</th>
                        <th class="p-3.5">Generic & Brand</th>
                        <th class="p-3.5 text-center">Units Sold</th>
                        <th class="p-3.5 text-right">Total Sales Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bestSellers as $index => $item)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3.5 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs
                                {{ $index == 0 ? 'bg-amber-100 text-amber-700 font-extrabold ring-2 ring-amber-400' : '' }}
                                {{ $index == 1 ? 'bg-slate-200 text-slate-700 font-bold' : '' }}
                                {{ $index == 2 ? 'bg-amber-50 text-amber-800 font-bold' : '' }}
                                {{ $index > 2 ? 'bg-gray-100 text-gray-600' : '' }}
                            ">
                                #{{ $index + 1 }}
                            </span>
                        </td>
                        <td class="p-3.5 font-bold text-gray-800">{{ $item->medicine->name ?? 'Deleted Medicine' }}</td>
                        <td class="p-3.5 text-gray-600">
                            {{ $item->medicine->generic_name ?? 'N/A' }}
                            <span class="block text-[10px] text-gray-400">{{ $item->medicine->brand ?? '' }}</span>
                        </td>
                        <td class="p-3.5 text-center font-extrabold text-blue-600">{{ number_format($item->total_qty_sold) }}</td>
                        <td class="p-3.5 text-right font-extrabold text-emerald-600">PKR {{ number_format($item->total_revenue_generated, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">No sales transactions available to compile best seller ranking.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
