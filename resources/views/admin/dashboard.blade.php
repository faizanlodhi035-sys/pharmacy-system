@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    
    <!-- 1. DASHBOARD HEADER & DATE PICKER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Welcome back, Admin! Here's what's happening today.</p>
        </div>
        <div class="flex items-center">
            <button class="bg-white border border-slate-200 shadow-xs hover:border-slate-300 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 flex items-center space-x-2.5 transition">
                <i class="fa-regular fa-calendar text-slate-500 text-sm"></i>
                <span>{{ now()->format('M d, Y') }}</span>
            </button>
        </div>
    </div>

    <!-- 2. TOP SUMMARY KPI CARDS (4 CARDS GRID) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- CARD 1: TOTAL SALES -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div class="bg-blue-50 text-blue-600 p-3 rounded-2xl group-hover:scale-105 transition">
                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                </div>
                <div class="w-20 h-10">
                    <!-- SVG Sparkline Blue -->
                    <svg viewBox="0 0 100 40" class="w-full h-full text-blue-500 fill-current opacity-20" preserveAspectRatio="none">
                        <path d="M0 35 Q 20 20, 40 28 T 80 10 T 100 5 L 100 40 L 0 40 Z"></path>
                    </svg>
                    <svg viewBox="0 0 100 40" class="w-full h-full text-blue-500 stroke-current -mt-10 fill-none" style="stroke-width: 2.5; stroke-linecap: round;">
                        <path d="M0 35 Q 20 20, 40 28 T 80 10 T 100 5"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-semibold text-slate-400">Total Sales</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">PKR {{ number_format($totalRevenue ?? 0, 2) }}</h3>
                <div class="flex items-center space-x-1.5 mt-2">
                    <span class="text-[11px] text-slate-400 font-medium">Real-time revenue</span>
                </div>
            </div>
        </div>

        <!-- CARD 2: TOTAL PURCHASES -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div class="bg-emerald-50 text-emerald-600 p-3 rounded-2xl group-hover:scale-105 transition">
                    <i class="fa-solid fa-bag-shopping text-xl"></i>
                </div>
                <div class="w-20 h-10">
                    <!-- SVG Sparkline Green -->
                    <svg viewBox="0 0 100 40" class="w-full h-full text-emerald-500 fill-current opacity-20" preserveAspectRatio="none">
                        <path d="M0 30 Q 25 35, 50 15 T 80 20 T 100 8 L 100 40 L 0 40 Z"></path>
                    </svg>
                    <svg viewBox="0 0 100 40" class="w-full h-full text-emerald-500 stroke-current -mt-10 fill-none" style="stroke-width: 2.5; stroke-linecap: round;">
                        <path d="M0 30 Q 25 35, 50 15 T 80 20 T 100 8"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-semibold text-slate-400">Total Purchases</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">PKR {{ number_format($totalPurchases ?? 0, 2) }}</h3>
                <div class="flex items-center space-x-1.5 mt-2">
                    <span class="text-[11px] text-slate-400 font-medium">Supplier procurement cost</span>
                </div>
            </div>
        </div>

        <!-- CARD 3: TOTAL MEDICINES -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div class="bg-purple-50 text-purple-600 p-3 rounded-2xl group-hover:scale-105 transition">
                    <i class="fa-solid fa-box text-xl"></i>
                </div>
                <div class="w-20 h-10">
                    <!-- SVG Sparkline Purple -->
                    <svg viewBox="0 0 100 40" class="w-full h-full text-purple-500 fill-current opacity-20" preserveAspectRatio="none">
                        <path d="M0 25 Q 30 10, 60 30 T 90 12 T 100 15 L 100 40 L 0 40 Z"></path>
                    </svg>
                    <svg viewBox="0 0 100 40" class="w-full h-full text-purple-500 stroke-current -mt-10 fill-none" style="stroke-width: 2.5; stroke-linecap: round;">
                        <path d="M0 25 Q 30 10, 60 30 T 90 12 T 100 15"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-semibold text-slate-400">Total Medicines</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalMedicines ?? 0) }}</h3>
                <div class="flex items-center space-x-1.5 mt-2">
                    <span class="text-[11px] text-slate-400 font-medium">Active product catalog</span>
                </div>
            </div>
        </div>

        <!-- CARD 4: TOTAL CUSTOMERS -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div class="bg-orange-50 text-orange-500 p-3 rounded-2xl group-hover:scale-105 transition">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <div class="w-20 h-10">
                    <!-- SVG Sparkline Orange -->
                    <svg viewBox="0 0 100 40" class="w-full h-full text-orange-500 fill-current opacity-20" preserveAspectRatio="none">
                        <path d="M0 35 Q 20 15, 50 25 T 80 10 T 100 20 L 100 40 L 0 40 Z"></path>
                    </svg>
                    <svg viewBox="0 0 100 40" class="w-full h-full text-orange-500 stroke-current -mt-10 fill-none" style="stroke-width: 2.5; stroke-linecap: round;">
                        <path d="M0 35 Q 20 15, 50 25 T 80 10 T 100 20"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-semibold text-slate-400">Total Customers</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalCustomers ?? 0) }}</h3>
                <div class="flex items-center space-x-1.5 mt-2">
                    <span class="text-[11px] text-slate-400 font-medium">Registered client base</span>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. MIDDLE SECTION (3 COLUMNS: Sales Overview, Top Selling, Low Stock) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        
        <!-- SALES OVERVIEW CHART (col-span-5) -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-extrabold text-slate-900 text-base">Sales Overview</h3>
                <select class="bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl px-3 py-1.5 focus:outline-none cursor-pointer">
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month">This Month</option>
                </select>
            </div>
            <div class="relative w-full h-64 flex items-center justify-center">
                <canvas id="salesOverviewChart"></canvas>
            </div>
        </div>

        <!-- TOP SELLING MEDICINES (col-span-4) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-extrabold text-slate-900 text-base">Top Selling Medicines</h3>
                    <a href="/reports/best-selling" class="text-xs font-bold text-slate-400 hover:text-blue-600 border border-slate-200 hover:border-blue-200 px-2.5 py-1 rounded-lg transition">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 font-bold">Medicine</th>
                                <th class="py-2.5 text-center font-bold">Sold Qty</th>
                                <th class="py-2.5 text-right font-bold">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/70 font-semibold text-slate-800">
                            @forelse($topSelling as $item)
                                <tr>
                                    <td class="py-2.5">
                                        <div class="flex items-center space-x-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-pills text-xs"></i>
                                            </div>
                                            <span class="font-bold text-slate-800 line-clamp-1" title="{{ $item->name }}">{{ $item->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 text-center text-slate-600">{{ number_format($item->sold_qty) }}</td>
                                    <td class="py-2.5 text-right text-slate-900 font-extrabold">PKR {{ number_format($item->revenue) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-slate-400">No sales recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- LOW STOCK ALERT (col-span-3) -->
        <div class="lg:col-span-3 bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-extrabold text-slate-900 text-base">Low Stock Alert</h3>
                    <a href="/reports/low-stock" class="text-xs font-bold text-slate-400 hover:text-blue-600 border border-slate-200 hover:border-blue-200 px-2.5 py-1 rounded-lg transition">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 font-bold">Medicine</th>
                                <th class="py-2.5 text-center font-bold">Stock</th>
                                <th class="py-2.5 text-right font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/70 font-semibold text-slate-800">
                            @forelse($lowStockList as $item)
                                <tr>
                                    <td class="py-2.5">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-7 h-7 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            </div>
                                            <span class="font-bold text-slate-800 line-clamp-1" title="{{ $item->name }}">{{ $item->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 text-center font-bold text-slate-900">{{ $item->stock }}</td>
                                    <td class="py-2.5 text-right">
                                        <span class="bg-red-50 text-red-600 border border-red-100 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">
                                            Low
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-slate-400">All products sufficiently stocked.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. LOWER SECTION (2 COLUMNS: Recent Invoices & Expiry Alerts) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- RECENT INVOICES (col-span-6) -->
        <div class="lg:col-span-6 bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Recent Invoices</h3>
                <a href="/sales" class="text-xs font-bold text-slate-400 hover:text-blue-600 border border-slate-200 hover:border-blue-200 px-2.5 py-1 rounded-lg transition">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px]">
                            <th class="py-2.5 font-bold">Invoice #</th>
                            <th class="py-2.5 font-bold">Customer</th>
                            <th class="py-2.5 font-bold">Date</th>
                            <th class="py-2.5 font-bold">Amount</th>
                            <th class="py-2.5 text-right font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/70 font-semibold text-slate-800">
                        @forelse($recentInvoices as $inv)
                            <tr>
                                <td class="py-3 font-bold text-slate-900">{{ $inv->invoice_number }}</td>
                                <td class="py-3 text-slate-600">{{ $inv->customer_name }}</td>
                                <td class="py-3 text-slate-500 font-normal">{{ $inv->date }}</td>
                                <td class="py-3 font-extrabold text-slate-900">PKR {{ number_format($inv->amount) }}</td>
                                <td class="py-3 text-right">
                                    <span class="bg-emerald-50 text-emerald-600 font-extrabold text-[11px] px-3 py-1 rounded-full">
                                        Paid
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400">No invoices generated yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EXPIRY ALERTS (col-span-6) -->
        <div class="lg:col-span-6 bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Expiry Alerts</h3>
                <a href="/expiry-alerts" class="text-xs font-bold text-slate-400 hover:text-blue-600 border border-slate-200 hover:border-blue-200 px-2.5 py-1 rounded-lg transition">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px]">
                            <th class="py-2.5 font-bold">Medicine</th>
                            <th class="py-2.5 font-bold">Expiry Date</th>
                            <th class="py-2.5 text-center font-bold">Days Left</th>
                            <th class="py-2.5 text-right font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/70 font-semibold text-slate-800">
                        @forelse($expiryList as $exp)
                            <tr>
                                <td class="py-3 font-bold text-slate-900">{{ $exp->name }}</td>
                                <td class="py-3 text-slate-500 font-normal">{{ $exp->expiry_date }}</td>
                                <td class="py-3 text-center font-extrabold text-amber-600">{{ $exp->days_left }}</td>
                                <td class="py-3 text-right">
                                    @if(($exp->status ?? 'Warning') === 'Warning')
                                        <span class="bg-amber-50 text-amber-700 font-extrabold text-[11px] px-3 py-1 rounded-full">
                                            Warning
                                        </span>
                                    @else
                                        <span class="bg-emerald-50 text-emerald-600 font-extrabold text-[11px] px-3 py-1 rounded-full">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">No expiring products in near future.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- 5. BOTTOM QUICK ACTIONS SECTION (8 CARDS GRID) -->
    <div class="space-y-3">
        <h3 class="font-extrabold text-slate-900 text-base">Quick Actions</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3.5">
            
            <!-- 1. Add Medicine -->
            <a href="/medicines" class="bg-white hover:bg-blue-50/50 border border-slate-200/90 hover:border-blue-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center group-hover:scale-110 transition shadow-sm">
                    <i class="fa-solid fa-plus text-base font-bold"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition">Add Medicine</span>
            </a>

            <!-- 2. New Purchase -->
            <a href="/purchases/create" class="bg-white hover:bg-emerald-50/50 border border-slate-200/90 hover:border-emerald-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-cart-arrow-down text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-600 transition">New Purchase</span>
            </a>

            <!-- 3. New Sale -->
            <a href="/pos" class="bg-white hover:bg-purple-50/50 border border-slate-200/90 hover:border-purple-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-basket-shopping text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-purple-600 transition">New Sale</span>
            </a>

            <!-- 4. Add Customer -->
            <a href="/customers" class="bg-white hover:bg-orange-50/50 border border-slate-200/90 hover:border-orange-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-user-plus text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-orange-500 transition">Add Customer</span>
            </a>

            <!-- 5. Stock Adjustment -->
            <a href="/medicines" class="bg-white hover:bg-cyan-50/50 border border-slate-200/90 hover:border-cyan-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-sliders text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-cyan-600 transition">Stock Adjustment</span>
            </a>

            <!-- 6. Expiry Report -->
            <a href="/expiry-alerts" class="bg-white hover:bg-red-50/50 border border-slate-200/90 hover:border-red-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-calendar-xmark text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-red-500 transition">Expiry Report</span>
            </a>

            <!-- 7. Sales Report -->
            <a href="/reports/sales" class="bg-white hover:bg-emerald-50/50 border border-slate-200/90 hover:border-emerald-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-chart-line text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-600 transition">Sales Report</span>
            </a>

            <!-- 8. Backup Data -->
            <a href="#" onclick="alert('System Backup Started! Automatic database snapshot saved.'); return false;" class="bg-white hover:bg-blue-50/50 border border-slate-200/90 hover:border-blue-300 p-3.5 rounded-2xl flex flex-col items-center justify-center text-center space-y-2.5 transition shadow-xs group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition">Backup Data</span>
            </a>

        </div>
    </div>

</div>

<!-- CHART.JS SCRIPT FOR SALES OVERVIEW -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('salesOverviewChart');
        if (ctx) {
            const chartCtx = ctx.getContext('2d');
            
            // Create Gradient Background
            const gradient = chartCtx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

            new Chart(chartCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Sales (PKR)',
                        data: {!! json_encode($salesChartData ?? [0,0,0,0,0,0,0]) !!},
                        borderColor: '#2563eb',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' PKR ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 10, weight: '600' },
                                callback: function(value) {
                                    return (value / 1000) + 'K';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection