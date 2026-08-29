<div class="space-y-6 p-6 bg-gray-50 min-h-screen">
    
    <!-- Top Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hold Invoices</h1>
            <p class="text-sm text-gray-500">Manage and restore paused POS sales invoices</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/pos" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-cash-register"></i>
                <span>Go to POS Counter</span>
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session()->has('message'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-700 text-sm rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 text-red-700 text-sm rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Held Invoices</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($totalHeldCount) }}</h3>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <i class="fa-solid fa-pause-circle text-2xl"></i>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Value Held</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">PKR {{ number_format($totalHeldAmount, 2) }}</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i class="fa-solid fa-money-bill-wave text-2xl"></i>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Restored</p>
                <h3 class="text-2xl font-black text-blue-600 mt-1">{{ number_format($totalRestoredCount) }}</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <i class="fa-solid fa-rotate-left text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT CARD -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5">
        
        <!-- TOOLBAR & FILTERS -->
        <div class="flex flex-col md:flex-row justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-search"></i>
                </span>
                <input type="text" wire:model.live="search" placeholder="Search reference number, customer name..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-600 transition">
            </div>

            <div class="flex items-center space-x-2">
                <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Status:</label>
                <select wire:model.live="statusFilter" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600">
                    <option value="held">Held (Active)</option>
                    <option value="restored">Restored</option>
                    <option value="all">All Statuses</option>
                </select>
            </div>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50/80 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Ref #</th>
                        <th class="px-4 py-3 font-semibold">Customer</th>
                        <th class="px-4 py-3 font-semibold">Items Summary</th>
                        <th class="px-4 py-3 font-semibold text-right">Total Amount</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-center">Date & Time</th>
                        <th class="px-4 py-3 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($holdInvoices as $hold)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-4 py-3.5 font-bold text-gray-900">
                                {{ $hold->reference_number }}
                            </td>
                            <td class="px-4 py-3.5 font-medium text-gray-700">
                                {{ $hold->customer_name ?: 'Walk-in Customer' }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600">
                                @php
                                    $items = is_array($hold->cart_data) ? $hold->cart_data : json_decode($hold->cart_data, true);
                                    $itemsSummary = collect($items ?? [])->map(fn($i) => ($i['name'] ?? 'Item') . ' (x' . ($i['qty'] ?? 1) . ')')->implode(', ');
                                @endphp
                                <span class="line-clamp-2" title="{{ $itemsSummary }}">
                                    {{ $itemsSummary ?: 'No items' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-black text-emerald-700">
                                PKR {{ number_format($hold->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($hold->status === 'held')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold">
                                        <i class="fa-solid fa-clock text-[10px] mr-1"></i> Held
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">
                                        <i class="fa-solid fa-check text-[10px] mr-1"></i> Restored
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs text-gray-500">
                                {{ $hold->created_at ? $hold->created_at->format('d M Y, h:i A') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button wire:click="restoreToPos({{ $hold->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                        <span>Restore to POS</span>
                                    </button>
                                    <button wire:click="deleteHold({{ $hold->id }})" onclick="confirm('Are you sure you want to delete this held invoice?') || event.stopImmediatePropagation()" class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                                <i class="fa-solid fa-pause-circle text-4xl text-gray-300 mb-2 block"></i>
                                No held invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="pt-2">
            {{ $holdInvoices->links() }}
        </div>
    </div>
</div>
