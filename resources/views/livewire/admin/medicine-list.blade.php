
<div class="space-y-6 p-6 bg-gray-50 min-h-screen">
    
    <!-- Top Header & Breadcrumb -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Management</h1>
            <p class="text-sm text-gray-500">Manage medicine inventory and stock levels</p>
        </div>
        <div class="text-sm text-gray-500">
            <span class="text-green-600 font-medium">Dashboard</span> > Stock Management
        </div>
    </div>

    <!-- Success / Error Alerts -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 text-green-700 text-sm rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 text-red-700 text-sm rounded shadow-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ADD MEDICINES FORM CARD -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center space-x-2 mb-4">
            <span class="text-green-600 text-lg">📦</span>
            <h2 class="text-lg font-bold text-gray-800">Add Medicines</h2>
        </div>

        <form action="/medicines/store" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Medicine Name</label>
                    <input type="text" name="name" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. Panadol 500mg" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Initial Quantity</label>
                    <input type="number" name="quantity" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. 100" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Category</label>
                    <select name="category_id" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Batch Number</label>
                    <input type="text" name="batch_number" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. BATCH-001">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers ?? [] as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Selling Price (PKR)</label>
                    <input type="number" step="0.01" name="selling_price" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. 50" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Purchase Price (PKR)</label>
                    <input type="number" step="0.01" name="purchase_price" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. 35">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="reset" class="px-5 py-2.5 border rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold shadow">Save Medicine</button>
            </div>
        </form>
    </div>

    <!-- MEDICINE STOCK LIST CARD -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center space-x-2">
            <span class="text-green-600 text-lg">📊</span>
            <h2 class="text-lg font-bold text-gray-800">Medicine Stock List</h2>
        </div>

        <!-- STATS CARDS ROW -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Total Medicines</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalMedicines) }}</h3>
                </div>
                <div class="p-3 bg-green-50 text-green-600 rounded-lg">💊</div>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Total Stock (Units)</p>
                    <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totalStock) }}</h3>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">📦</div>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Low Stock</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $lowStock }}</h3>
                </div>
                <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">⚠️</div>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Expired</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $expired }}</h3>
                </div>
                <div class="p-3 bg-red-50 text-red-600 rounded-lg">❌</div>
            </div>
        </div>

        <!-- SEARCH & FILTER TOOLBAR -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" placeholder="Search medicine..." class="p-2 border rounded-lg text-sm bg-gray-50 outline-none focus:bg-white focus:ring-1 focus:ring-green-500">
            <select class="p-2 border rounded-lg text-sm bg-gray-50 outline-none">
                <option>All Categories</option>
            </select>
            <select class="p-2 border rounded-lg text-sm bg-gray-50 outline-none">
                <option>All Suppliers</option>
            </select>
            <select class="p-2 border rounded-lg text-sm bg-gray-50 outline-none">
                <option>Stock Status</option>
            </select>
            <div class="flex space-x-2">
                <button class="flex-1 bg-green-600 text-white rounded-lg text-sm font-semibold py-2 hover:bg-green-700">Filter</button>
                <button class="flex-1 border border-gray-300 rounded-lg text-sm font-semibold py-2 hover:bg-gray-50 text-gray-700">Export</button>
            </div>
        </div>

        <!-- DATA TABLE -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">#</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Medicine Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Category</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Batch Details</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Total Stock</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Selling Price</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Expiry Date</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($medicines as $index => $medicine)
                        @php
                            $totalQty = $medicine->batches->sum('quantity');
                            $firstBatch = $medicine->batches->first();
                            $statusClass = $totalQty > 15 ? 'bg-green-50 text-green-700' : ($totalQty > 0 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700');
                            $statusText = $totalQty > 15 ? 'In Stock' : ($totalQty > 0 ? 'Low Stock' : 'Out of Stock');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900">{{ $medicine->name }}</div>
                                @if($medicine->generic_name)
                                    <div class="text-xs text-gray-400">{{ $medicine->generic_name }}</div>
                                @endif
                                @if($medicine->packagings->count() > 1)
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($medicine->packagings as $pkg)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $pkg->display_name ?: $pkg->unit?->name }} ({{ (int)$pkg->conversion_to_base }}x)
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $medicine->category->name ?? 'General' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                @foreach($medicine->batches as $batch)
                                    <div class="font-mono">{{ $batch->batch_number }} ({{ number_format($batch->quantity) }} {{ $medicine->base_unit ?: 'units' }})</div>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-sm text-gray-900">
                                    {{ number_format($totalQty) }} <span class="text-xs font-normal text-gray-500">{{ $medicine->base_unit ?: 'Base Units' }}</span>
                                </div>
                                @if($totalQty > 0 && $medicine->packagings->count() > 1)
                                    <div class="text-[11px] font-medium text-emerald-700 mt-0.5">
                                        {{ $medicine->formatStockInUnits($totalQty) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-blue-600">PKR {{ number_format($firstBatch->selling_price ?? $medicine->unit_price ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($firstBatch?->expiry_date)
                                    {{ $firstBatch->expiry_date->format('Y-m-d') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                No medicines found in inventory.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
