@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-blue-600 font-semibold mb-1">
                <a href="/reports" class="hover:underline">Reports</a>
                <span>/</span>
                <span>Expiry Report</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">4. Expiry Report</h2>
            <p class="text-xs text-gray-500 mt-1">Expired medicine batches and upcoming expiries with cost risk analysis</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-xs shadow hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-2">
        <a href="/reports/expiry?status=all" class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $status == 'all' ? 'bg-slate-800 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All Expiry Risks
        </a>
        <a href="/reports/expiry?status=expired" class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $status == 'expired' ? 'bg-red-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Already Expired
        </a>
        <a href="/reports/expiry?status=30days" class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $status == '30days' ? 'bg-amber-500 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Expiring in 30 Days
        </a>
        <a href="/reports/expiry?status=60days" class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $status == '60days' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Expiring in 60 Days
        </a>
        <a href="/reports/expiry?status=90days" class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $status == '90days' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Expiring in 90 Days
        </a>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-red-500">
            <p class="text-xs font-semibold text-gray-400 uppercase">Already Expired Loss Valuation</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">PKR {{ number_format($expiredValue, 2) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500">
            <p class="text-xs font-semibold text-gray-400 uppercase">30-Day Expiry Risk Valuation</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">PKR {{ number_format($atRiskValue, 2) }}</h3>
        </div>
    </div>

    <!-- Expiry Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Expired & Expiring Batches</h3>
            <span class="text-xs text-gray-400 font-semibold">{{ $batches->count() }} batches</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase border-b border-gray-100 font-semibold">
                    <tr>
                        <th class="p-3.5">Medicine Name</th>
                        <th class="p-3.5">Batch #</th>
                        <th class="p-3.5">Expiry Date</th>
                        <th class="p-3.5 text-center">Remaining Stock</th>
                        <th class="p-3.5 text-right">Purchase Price</th>
                        <th class="p-3.5 text-right">Risk Valuation</th>
                        <th class="p-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batches as $batch)
                    @php
                        $isExpired = \Carbon\Carbon::parse($batch->expiry_date)->isPast();
                        $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($batch->expiry_date), false);
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3.5 font-bold text-gray-800">{{ $batch->medicine->name ?? 'N/A' }}</td>
                        <td class="p-3.5 font-mono text-gray-600">{{ $batch->batch_number }}</td>
                        <td class="p-3.5 font-semibold {{ $isExpired ? 'text-red-600' : 'text-amber-600' }}">{{ $batch->expiry_date }}</td>
                        <td class="p-3.5 text-center font-bold text-gray-800">{{ number_format($batch->quantity) }}</td>
                        <td class="p-3.5 text-right text-gray-600">PKR {{ number_format($batch->purchase_price, 2) }}</td>
                        <td class="p-3.5 text-right font-bold text-red-600">PKR {{ number_format($batch->quantity * $batch->purchase_price, 2) }}</td>
                        <td class="p-3.5">
                            @if($isExpired)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Expired</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Expiring in {{ $daysLeft }} days</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">No expiring medicine batches found for this criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
