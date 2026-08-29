@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-blue-600 font-semibold mb-1">
                <a href="/reports" class="hover:underline">Reports</a>
                <span>/</span>
                <span>Profit & Loss Statement</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">5. Profit & Loss Report</h2>
            <p class="text-xs text-gray-500 mt-1">Financial profit and loss analysis comparing sales revenue, COGS and net margins</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-xs shadow hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Statement</span>
            </button>
        </div>
    </div>

    <!-- Date Filter Form -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="/reports/profit-loss" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-slate-800 text-white py-2 px-4 rounded-lg text-xs font-bold hover:bg-slate-900 transition">Generate P&L</button>
                <a href="/reports/profit-loss" class="bg-gray-100 text-gray-600 py-2 px-4 rounded-lg text-xs font-bold hover:bg-gray-200 transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- P&L Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Gross Sales Revenue</p>
            <h3 class="text-3xl font-bold text-blue-600 mt-2">PKR {{ number_format($totalSalesRevenue, 2) }}</h3>
            <p class="text-[11px] text-gray-400 mt-1">Total billing before discounts</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Cost of Goods Sold (COGS)</p>
            <h3 class="text-3xl font-bold text-slate-700 mt-2">PKR {{ number_format($totalCogs, 2) }}</h3>
            <p class="text-[11px] text-gray-400 mt-1">Direct purchase cost of items sold</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 {{ $netProfit >= 0 ? 'border-l-emerald-500' : 'border-l-red-500' }}">
            <p class="text-xs font-semibold text-gray-400 uppercase">Net Profit / Margin</p>
            <h3 class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-2">
                PKR {{ number_format($netProfit, 2) }}
            </h3>
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $netProfit >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                Profit Margin: {{ number_format($profitMargin, 1) }}%
            </span>
        </div>
    </div>

    <!-- P&L Financial Statement Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3">Income & Expenditure Statement</h3>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="font-medium text-gray-700">Gross Sales Revenue (+)</span>
                <span class="font-bold text-gray-900">PKR {{ number_format($totalSalesRevenue, 2) }}</span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="font-medium text-gray-700">Cost of Goods Sold (COGS) (-)</span>
                <span class="font-bold text-red-600">- PKR {{ number_format($totalCogs, 2) }}</span>
            </div>

            <div class="flex justify-between py-2 bg-gray-50 px-3 rounded-lg font-bold">
                <span class="text-gray-800">Gross Operating Profit</span>
                <span class="text-blue-600">PKR {{ number_format($grossProfit, 2) }}</span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="font-medium text-gray-700">Discounts & Concessions Given (-)</span>
                <span class="font-bold text-amber-600">- PKR {{ number_format($totalDiscountsGiven, 2) }}</span>
            </div>

            <div class="flex justify-between py-3 bg-emerald-50 px-4 rounded-xl font-extrabold text-base border border-emerald-100">
                <span class="text-emerald-900">NET REALIZED PROFIT</span>
                <span class="text-emerald-700">PKR {{ number_format($netProfit, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
