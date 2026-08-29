@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & BREADCRUMB --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Discount Analysis Report</h2>
            <p class="text-xs text-gray-500 mt-1">Detailed audit of all discounts given on POS and sales invoices</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="/reports" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Reports Hub</span>
            </a>

            <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    {{-- 2. KPI SUMMARY CARDS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- CARD 1: TOTAL DISCOUNT GIVEN -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-pink-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Discount Given</p>
                <h3 class="text-2xl font-black text-pink-600 mt-1">PKR {{ number_format($totalDiscountGiven ?? 0, 2) }}</h3>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Cumulative value of all discounts</p>
            </div>
            <div class="bg-pink-50 text-pink-600 p-3.5 rounded-2xl">
                <i class="fa-solid fa-percent text-2xl"></i>
            </div>
        </div>

        <!-- CARD 2: DISCOUNTED INVOICES -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-purple-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Discounted Invoices</p>
                <h3 class="text-2xl font-black text-purple-600 mt-1">{{ number_format($discountedInvoicesCount ?? 0) }}</h3>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Total sales with discount applied</p>
            </div>
            <div class="bg-purple-50 text-purple-600 p-3.5 rounded-2xl">
                <i class="fa-solid fa-receipt text-2xl"></i>
            </div>
        </div>

        <!-- CARD 3: AVERAGE DISCOUNT PER INVOICE -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-blue-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Avg Discount / Sale</p>
                <h3 class="text-2xl font-black text-blue-600 mt-1">PKR {{ number_format($avgDiscountPerInvoice ?? 0, 2) }}</h3>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Average concession amount</p>
            </div>
            <div class="bg-blue-50 text-blue-600 p-3.5 rounded-2xl">
                <i class="fa-solid fa-calculator text-2xl"></i>
            </div>
        </div>

        <!-- CARD 4: DISCOUNT RATIO (% OF SALES) -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-amber-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Discount Ratio</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($discountPercentageOfSales ?? 0, 1) }}%</h3>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Discount as % of gross subtotal</p>
            </div>
            <div class="bg-amber-50 text-amber-600 p-3.5 rounded-2xl">
                <i class="fa-solid fa-chart-pie text-2xl"></i>
            </div>
        </div>

    </div>

    {{-- 3. FILTER TOOLBAR --}}
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-gray-100">
        <form method="GET" action="{{ route('reports.discounts') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
            
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Time Period</label>
                <select name="filter" onchange="this.form.submit()" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 outline-none focus:border-pink-500 focus:bg-white">
                    <option value="today" {{ ($filter ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ ($filter ?? '') === 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ ($filter ?? '') === 'monthly' ? 'selected' : '' }}>This Month</option>
                    <option value="custom" {{ ($filter ?? '') === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Cashier / User</label>
                <select name="user_id" onchange="this.form.submit()" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 outline-none focus:border-pink-500 focus:bg-white">
                    <option value="">All Cashiers & Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ ($userId ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-700 outline-none focus:border-pink-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-700 outline-none focus:border-pink-500 focus:bg-white">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 h-10 bg-pink-600 hover:bg-pink-700 text-white font-bold rounded-xl text-xs shadow-xs transition">
                    Filter
                </button>
                <a href="{{ route('reports.discounts') }}" class="h-10 px-3.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl text-xs transition flex items-center justify-center">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- 4. DETAILED DISCOUNTED SALES TABLE --}}
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-base">Discounted Sales Invoices</h3>
                <p class="text-xs text-gray-500">List of all invoices where discounts were granted</p>
            </div>
            <span class="text-xs font-bold text-pink-700 bg-pink-50 px-3 py-1 rounded-full border border-pink-200">
                {{ count($discountSales ?? []) }} Invoices Found
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-gray-100 text-xs text-slate-500 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">Date & Time</th>
                        <th class="p-4">Invoice #</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Cashier / User</th>
                        <th class="p-4 text-right">Subtotal</th>
                        <th class="p-4 text-right">Discount (PKR)</th>
                        <th class="p-4 text-right">Paid Amount</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-xs">
                    @forelse($discountSales ?? [] as $index => $sale)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-semibold text-gray-500">{{ $index + 1 }}</td>
                            <td class="p-4 font-medium text-gray-600">
                                {{ $sale->created_at->format('d M, Y') }}
                                <span class="text-[10px] text-gray-400 block">{{ $sale->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="p-4 font-mono font-bold text-slate-900">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-gray-800">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</span>
                                @if($sale->customer?->phone)
                                    <span class="text-[10px] text-gray-400 block">{{ $sale->customer->phone }}</span>
                                @endif
                            </td>
                            <td class="p-4 font-medium text-gray-700">
                                {{ $sale->user?->name ?? 'Admin' }}
                            </td>
                            <td class="p-4 text-right font-medium text-gray-600">
                                PKR {{ number_format($sale->subtotal ?? ($sale->total_amount + $sale->discount), 2) }}
                            </td>
                            <td class="p-4 text-right font-black text-pink-600 bg-pink-50/50">
                                - PKR {{ number_format($sale->discount, 2) }}
                            </td>
                            <td class="p-4 text-right font-bold text-emerald-700">
                                PKR {{ number_format($sale->total_amount, 2) }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('sales.show', $sale->id) }}" class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white px-2.5 py-1 rounded-lg font-bold text-[11px] transition">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-percent text-gray-400 text-lg"></i>
                                </div>
                                <p class="text-sm font-semibold">No discounted sales found for this filter period.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($discountSales ?? []) > 0)
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-xs">
                    <tr>
                        <td colspan="5" class="p-4 text-slate-800 uppercase tracking-wider text-right">Totals:</td>
                        <td class="p-4 text-right text-slate-800">PKR {{ number_format($totalOriginalSubtotal ?? 0, 2) }}</td>
                        <td class="p-4 text-right text-pink-600 font-black">PKR {{ number_format($totalDiscountGiven ?? 0, 2) }}</td>
                        <td class="p-4 text-right text-emerald-700 font-black">PKR {{ number_format($totalFinalAmountPaid ?? 0, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection
