<div class="space-y-6 p-6 bg-slate-50 min-h-screen">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Medicine & Product Inventory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage medicine stock levels, batches, pricing, and lifecycle</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="/medicines" 
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition"
            >
                <span>+ Add New Product</span>
            </a>
            <button
                type="button"
                wire:click="exportCsv"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition"
            >
                <span>Export CSV</span>
            </button>
        </div>
    </div>

    {{-- Flash Alerts --}}
    @if(session()->has('message'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    @if(session()->has('warning'))
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span>⚠️</span>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">✕</button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider font-semibold text-emerald-700">Total Products</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalMedicines) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $totalMedicineProducts }} Medicines • {{ $totalGeneralProducts }} General</p>
        </div>

        <div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider font-semibold text-blue-700">Total Stock (Units)</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalStock) }}</p>
            <p class="text-[11px] text-blue-600 mt-1 font-medium">Est. Value: PKR {{ number_format($totalStockValue, 0) }}</p>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider font-semibold text-amber-700">Low Stock Alert</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($lowStock) }}</p>
            <p class="text-[11px] text-amber-600 mt-1">Needs reordering</p>
        </div>

        <div class="rounded-2xl border border-red-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider font-semibold text-red-700">Expired Items</p>
            <p class="text-2xl font-black text-red-600 mt-1">{{ number_format($expired) }}</p>
            <p class="text-[11px] text-red-500 mt-1">Requires disposal</p>
        </div>
    </div>

    {{-- Main Section --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        {{-- Filters --}}
        <div class="p-4 border-b border-slate-100 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search name, barcode, batch..."
                    class="h-10 px-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-300 outline-none"
                >

                <select wire:model.live="productTypeFilter" class="h-10 px-3 border border-slate-200 rounded-xl bg-white text-sm">
                    <option value="all">All Types</option>
                    <option value="medicine">Medicines</option>
                    <option value="general">General Store</option>
                </select>

                <select wire:model.live="categoryFilter" class="h-10 px-3 border border-slate-200 rounded-xl bg-white text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="stockFilter" class="h-10 px-3 border border-slate-200 rounded-xl bg-white text-sm">
                    <option value="">Stock Status</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                    <option value="expired">Expired</option>
                </select>

                <button
                    type="button"
                    wire:click="$set('search', ''); $set('productTypeFilter', 'all'); $set('categoryFilter', ''); $set('stockFilter', '');"
                    class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition"
                >
                    Reset Filters
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5">#</th>
                        <th class="px-4 py-3.5">Product Name</th>
                        <th class="px-4 py-3.5">Category</th>
                        <th class="px-4 py-3.5">Batches</th>
                        <th class="px-4 py-3.5">Stock</th>
                        <th class="px-4 py-3.5">Price</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($medicines as $index => $medicine)
                        @php
                            $stock = $medicine->batches->sum('quantity');
                            $alert = $medicine->alert_quantity ?? 10;
                            $firstBatch = $medicine->batches->first();
                            $status = $stock > $alert ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock');
                            $statusClass = $stock > $alert ? 'bg-emerald-50 text-emerald-700' : ($stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700');
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-xs text-slate-400">{{ $medicines->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $medicine->name }}</div>
                                @if($medicine->generic_name)
                                    <div class="text-xs text-slate-400">{{ $medicine->generic_name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $medicine->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs font-mono">
                                {{ $medicine->batches->count() }} batches
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                {{ number_format($stock) }} {{ $medicine->base_unit }}
                            </td>
                            <td class="px-4 py-3 font-black text-slate-900">
                                PKR {{ number_format($medicine->selling_price, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openViewModal({{ $medicine->id }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">👁️</button>
                                    <button wire:click="openEditModal({{ $medicine->id }})" class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs">✏️</button>
                                    <button wire:click="openAddBatchModal({{ $medicine->id }})" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs">➕</button>
                                    <button wire:click="confirmDelete({{ $medicine->id }})" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $medicines->links() }}
        </div>

    </div>

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto text-xl">🗑️</div>
                <h3 class="text-base font-bold text-slate-900">Delete Product?</h3>
                <p class="text-xs text-slate-600">Are you sure you want to delete <strong>{{ $deleteMedicineName }}</strong>?</p>
                <div class="flex gap-2">
                    <button wire:click="closeDeleteModal" class="flex-1 py-2 rounded-xl bg-slate-100 text-xs font-bold">Cancel</button>
                    <button wire:click="deleteMedicine" class="flex-1 py-2 rounded-xl bg-red-600 text-white text-xs font-bold">Confirm</button>
                </div>
            </div>
        </div>
    @endif

</div>
