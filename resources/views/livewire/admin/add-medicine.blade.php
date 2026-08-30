<div class="min-h-screen bg-slate-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">

            <div>
                <h1 class="text-3xl font-bold text-slate-900">
                    Products & Inventory
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Manage medicine and general store inventory items
                </p>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <a
                    href="{{ route('medicines.bulk-add') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition"
                >
                    <i class="fa-solid fa-plus-square"></i>
                    <span>+ Bulk Add Medicines</span>
                </a>

                <a
                    href="/dashboard"
                    class="text-emerald-600 font-medium hover:text-emerald-700"
                >
                    Dashboard
                </a>

                <span class="text-slate-400">›</span>

                <span class="text-slate-500">
                    Products & Inventory
                </span>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGE --}}
        {{-- ========================================================= --}}

        @if (session()->has('message'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-700">
                {{ session('message') }}
            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- VALIDATION --}}
        {{-- ========================================================= --}}

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- ADD PRODUCT FORM --}}
        {{-- ========================================================= --}}

        <section class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10L4 17V7m8 14V11m0 0L4 7m8 4l8-4"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Add Inventory Product
                        </h2>

                        <p class="text-xs text-slate-500">
                            Add a new medicine or general store item to inventory
                        </p>
                    </div>
                </div>

                <a
                    href="{{ route('medicines.bulk-add') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition"
                >
                    <i class="fa-solid fa-layer-group"></i>
                    <span>+ Bulk Add Medicines</span>
                </a>

            </div>

            {{-- Product Type Selector Tabs --}}
            <div class="px-5 pt-4 pb-2 border-b border-slate-100 bg-slate-50/50">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                    Select Product Type
                </label>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        wire:click="setProductType('medicine')"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 {{ $product_type === 'medicine' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}"
                    >
                        <i class="fa-solid fa-capsules text-base"></i>
                        <span>Medicine</span>
                    </button>

                    <button
                        type="button"
                        wire:click="setProductType('general')"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 {{ $product_type === 'general' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}"
                    >
                        <i class="fa-solid fa-store text-base"></i>
                        <span>General Store Item</span>
                    </button>
                </div>
            </div>


            <form wire:submit="save" class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-4">

                    {{-- Product Name --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Product Name <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            wire:model.live.debounce.300ms="name"
                            class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                            placeholder="{{ $product_type === 'general' ? 'e.g. Lux Soap 100g' : 'e.g. Panadol 500mg' }}"
                            autocomplete="off"
                        >
                    </div>

                    {{-- Category (Smart Searchable Auto-Suggest Dropdown & Quick Create) --}}
                    <div 
                        x-data="{
                            open: false,
                            search: @entangle('category_search').live,
                            selectedId: @entangle('category_id').live,
                            categories: {{ Js::from($formCategories->map(fn($c) => ['id' => (string)$c->id, 'name' => $c->name, 'type' => $c->product_type ?? 'both'])) }},
                            get filtered() {
                                if (!this.search || this.search.trim() === '') return this.categories;
                                const q = this.search.toLowerCase().trim();
                                return this.categories.filter(c => c.name.toLowerCase().includes(q));
                            },
                            get exactMatch() {
                                if (!this.search) return null;
                                const q = this.search.toLowerCase().trim();
                                return this.categories.find(c => c.name.toLowerCase() === q);
                            },
                            select(cat) {
                                this.selectedId = cat.id;
                                this.search = cat.name;
                                $wire.selectCategory(cat.id, cat.name);
                                this.open = false;
                            },
                            clear() {
                                this.selectedId = '';
                                this.search = '';
                                $wire.clearCategory();
                                this.open = false;
                            },
                            createCategory() {
                                if (this.search && this.search.trim() !== '') {
                                    $wire.quickCreateCategory(this.search.trim());
                                    this.open = false;
                                }
                            }
                        }"
                        class="relative"
                        @click.outside="open = false"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-slate-700">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i> Auto-Suggest
                            </span>
                        </div>

                        <div class="relative">
                            <input
                                type="text"
                                x-model="search"
                                @focus="open = true"
                                @input="open = true"
                                @keydown.escape="open = false"
                                placeholder="Type or select category..."
                                class="w-full h-10 pl-3 pr-16 border rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 {{ $errors->has('category_id') ? 'border-red-300 ring-1 ring-red-300' : 'border-slate-200' }}"
                                autocomplete="off"
                            >

                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                                <template x-if="search && search.length > 0">
                                    <button 
                                        type="button" 
                                        @click="clear()" 
                                        class="p-1 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100"
                                        title="Clear Category"
                                    >
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </template>
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="p-1 text-slate-400 hover:text-slate-600"
                                    title="Toggle Dropdown"
                                >
                                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Hidden Input to retain exact Livewire Category binding --}}
                        <input type="hidden" wire:model="category_id" value="{{ $category_id }}">

                        {{-- Dynamic Suggestion Dropdown Panel --}}
                        <div 
                            x-show="open" 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                            class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-100 py-1"
                            style="display: none;"
                        >
                            {{-- Header Indicator --}}
                            <div class="px-3 py-1.5 bg-slate-50 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <span>Suggested Categories</span>
                                <span x-text="filtered.length + ' available'"></span>
                            </div>

                            {{-- Filtered Suggestions List --}}
                            <template x-for="cat in filtered" :key="cat.id">
                                <button
                                    type="button"
                                    @click="select(cat)"
                                    class="w-full text-left px-3.5 py-2.5 hover:bg-emerald-50/80 flex items-center justify-between text-sm transition group"
                                    :class="String(selectedId) === String(cat.id) ? 'bg-emerald-50 font-bold text-emerald-900' : 'text-slate-700'"
                                >
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-tag text-xs text-emerald-600 group-hover:scale-110 transition-transform"></i>
                                        <span x-text="cat.name"></span>
                                    </div>
                                    <template x-if="String(selectedId) === String(cat.id)">
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">
                                            <i class="fa-solid fa-check text-[10px]"></i> Selected
                                        </span>
                                    </template>
                                </button>
                            </template>

                            {{-- No Match / Quick Add New Category Option --}}
                            <template x-if="filtered.length === 0 && search && search.trim() !== ''">
                                <div class="p-3 text-center">
                                    <p class="text-xs text-slate-500 mb-2">
                                        No matching category found for "<span class="font-bold text-slate-700" x-text="search"></span>"
                                    </p>
                                    <button
                                        type="button"
                                        @click="createCategory()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition"
                                    >
                                        <i class="fa-solid fa-plus-circle"></i>
                                        <span>Add "<span x-text="search"></span>" as New Category</span>
                                    </button>
                                </div>
                            </template>

                            {{-- Quick Add Option if typed name doesn't exactly match any existing --}}
                            <template x-if="filtered.length > 0 && search && search.trim() !== '' && !exactMatch">
                                <div class="p-2 bg-emerald-50/50 border-t border-emerald-100">
                                    <button
                                        type="button"
                                        @click="createCategory()"
                                        class="w-full text-left px-3 py-1.5 bg-white hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-800 flex items-center justify-between transition"
                                    >
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-plus text-emerald-600"></i>
                                            <span>Create & Select: "<span x-text="search"></span>"</span>
                                        </span>
                                        <span class="text-[10px] bg-emerald-600 text-white px-1.5 py-0.5 rounded">New Category</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Brand / Company (with Auto-Suggestions) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-slate-700">
                                Brand / Company
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium">Auto-suggest</span>
                        </div>

                        <input
                            type="text"
                            list="brand-suggestions"
                            wire:model="brand"
                            class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                            placeholder="{{ $product_type === 'general' ? 'e.g. Unilever / Lux / Nestle' : 'e.g. GSK / Abbott / Getz' }}"
                            autocomplete="off"
                        >
                    </div>

                    @if($product_type === 'medicine')
                        {{-- Generic Name (with Auto-Suggestions) --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-slate-700">
                                    Generic Name
                                </label>
                                <span class="text-[10px] text-slate-400 font-medium">Auto-suggest</span>
                            </div>

                            <input
                                type="text"
                                list="generic-suggestions"
                                wire:model="generic_name"
                                class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                                placeholder="e.g. Paracetamol / Ibuprofen / Amoxicillin"
                                autocomplete="off"
                            >
                        </div>

                        {{-- Manufacturer (with Auto-Suggestions) --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-slate-700">
                                    Manufacturer
                                </label>
                                <span class="text-[10px] text-slate-400 font-medium">Auto-suggest</span>
                            </div>

                            <input
                                type="text"
                                list="manufacturer-suggestions"
                                wire:model="manufacturer"
                                class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                                placeholder="e.g. GlaxoSmithKline / Abbott / Getz Pharma"
                                autocomplete="off"
                            >
                        </div>

                        {{-- Strength & Dosage Form --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-slate-700">
                                    Strength
                                </label>
                                <span class="text-[10px] text-slate-400 font-medium">e.g. 500mg</span>
                            </div>

                            <input
                                type="text"
                                list="strength-suggestions"
                                wire:model="strength"
                                class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                                placeholder="e.g. 500mg, 250mg, 10mg"
                                autocomplete="off"
                            >
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-slate-700">
                                    Dosage Form
                                </label>
                                <span class="text-[10px] text-slate-400 font-medium">e.g. Tablet</span>
                            </div>

                            <input
                                type="text"
                                list="dosage-form-suggestions"
                                wire:model="dosage_form"
                                class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                                placeholder="e.g. Tablet, Capsule, Syrup, Injection"
                                autocomplete="off"
                            >
                        </div>
                    @endif

                    {{-- Initial Quantity & Unit Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Initial Stock Quantity <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-2 gap-2">
                            <input
                                type="number"
                                min="0"
                                step="any"
                                wire:model.live="quantity"
                                class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                                placeholder="e.g. 5"
                            >

                            <select
                                wire:model.live="initial_stock_unit"
                                class="w-full h-10 px-2 border border-slate-200 rounded-lg bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option value="base">{{ $base_unit ?: 'Base Unit' }} (1x)</option>
                                @if(!empty($secondary_unit))
                                    <option value="secondary">{{ $secondary_unit }} ({{ $this->calculatedSecondaryConversion }}x Base)</option>
                                @endif
                                @if(!empty($primary_unit))
                                    <option value="primary">{{ $primary_unit }} ({{ $this->calculatedPrimaryConversion }}x Base)</option>
                                @endif
                            </select>
                        </div>
                        @if((float)$quantity > 0)
                            @php
                                $cMultiplier = 1;
                                if ($initial_stock_unit === 'primary') $cMultiplier = $this->calculatedPrimaryConversion;
                                elseif ($initial_stock_unit === 'secondary') $cMultiplier = $this->calculatedSecondaryConversion;
                                $calcBaseQty = ((float)$quantity) * $cMultiplier;
                            @endphp
                            <p class="text-[11px] text-emerald-700 font-medium mt-1">
                                = {{ number_format($calcBaseQty) }} {{ $base_unit ?: 'Tablet' }}s stored in Inventory
                            </p>
                        @endif
                    </div>

                    {{-- Purchase Price (Base Unit default) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Base Unit Purchase Price (PKR) <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            wire:model.live="purchase_price"
                            class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                            placeholder="e.g. 8"
                        >
                    </div>

                    {{-- Selling Price (Base Unit default) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Base Unit Selling Price (PKR) <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            wire:model.live="selling_price"
                            class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                            placeholder="e.g. 12"
                        >
                    </div>

                    {{-- Alert Quantity --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Low Stock Alert Qty (Base Units)
                        </label>

                        <input
                            type="number"
                            min="0"
                            wire:model="alert_quantity"
                            class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                            placeholder="10"
                        >
                    </div>

                    {{-- Supplier --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Supplier
                        </label>

                        <select
                            wire:model="supplier_id"
                            class="w-full h-10 px-3 border border-slate-200 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                        >
                            <option value="">
                                Select Supplier
                            </option>

                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Expiry & Batch Options for General Items --}}
                    @if($product_type === 'general')
                        <div class="col-span-full grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-3 rounded-lg border border-slate-200 my-1">
                            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                                <input type="checkbox" wire:model.live="has_expiry" class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                <span>Track Expiry Date for this product</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                                <input type="checkbox" wire:model.live="track_batches" class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                <span>Track Batch Numbers for this product</span>
                            </label>
                        </div>
                    @endif

                    {{-- Expiry Date --}}
                    @if($product_type === 'medicine' || $has_expiry)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Expiry Date {{ ($product_type === 'medicine' || $has_expiry) ? '*' : '' }}
                            </label>

                            <input
                                type="date"
                                wire:model="expiry_date"
                                class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                            >
                        </div>
                    @endif

                    {{-- Batch Number --}}
                    @if($product_type === 'medicine' || $track_batches)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Batch Number {{ $product_type === 'medicine' ? '*' : '(Optional)' }}
                            </label>

                            <input
                                type="text"
                                wire:model="batch_number"
                                class="w-full h-10 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                                placeholder="e.g. BATCH-001"
                            >
                        </div>
                    @endif

                </div>

                {{-- ========================================================= --}}
                {{-- MEDICINE UNITS & PACKAGING HIERARCHY (FOR MEDICINE) --}}
                {{-- ========================================================= --}}
                @if($product_type === 'medicine')
                    <div class="mt-6 pt-5 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                    <i class="fa-solid fa-boxes-stacked text-emerald-600"></i>
                                    Medicine Packaging & Unit Hierarchy
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Configure multi-level packaging units. Inventory is always stored in Base Units.
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-semibold">
                                <i class="fa-solid fa-shield-check"></i> Base Unit Stock Architecture
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            {{-- 1. BASE UNIT --}}
                            <div class="bg-emerald-50/50 p-4 rounded-xl border-2 border-emerald-300 relative shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-dot text-emerald-600 text-[10px]"></i> Base Unit (Level 1)
                                    </span>
                                    <span class="px-2 py-0.5 bg-emerald-600 text-white text-[10px] font-bold rounded-md">1 Base Unit</span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Base Unit Name <span class="text-red-500">*</span></label>
                                        <input type="text" list="base-units-list" wire:model.live="base_unit" class="w-full h-9 px-3 border border-emerald-300 rounded-lg text-sm bg-white font-medium" placeholder="Tablet">
                                        <datalist id="base-units-list">
                                            @foreach($availableUnits as $unit)
                                                <option value="{{ $unit->name }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Purchase Price</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rs.</span>
                                                <input type="number" min="0" step="0.01" wire:model.live="base_unit_purchase_price" class="w-full h-8 pl-8 pr-2 border border-slate-300 rounded-md text-xs bg-white" placeholder="{{ $purchase_price ?: '8' }}">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Sale Price</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rs.</span>
                                                <input type="number" min="0" step="0.01" wire:model.live="base_unit_selling_price" class="w-full h-8 pl-8 pr-2 border border-slate-300 rounded-md text-xs bg-white font-bold text-emerald-700" placeholder="{{ $selling_price ?: '12' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Base Barcode (Optional)</label>
                                        <input type="text" wire:model="base_unit_barcode" class="w-full h-8 px-2 border border-slate-300 rounded-md text-xs bg-white" placeholder="Scan or enter barcode">
                                    </div>
                                </div>
                            </div>

                            {{-- 2. SECONDARY UNIT --}}
                            <div class="bg-blue-50/40 p-4 rounded-xl border border-blue-200 relative shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-blue-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-arrow-up text-blue-500 text-[10px]"></i> Secondary Unit (Level 2)
                                    </span>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-md">
                                        = {{ $this->calculatedSecondaryConversion }} {{ $base_unit ?: 'Tablets' }}
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Secondary Unit (e.g. Strip)</label>
                                        <input type="text" list="sec-units-list" wire:model.live="secondary_unit" class="w-full h-9 px-3 border border-blue-200 rounded-lg text-sm bg-white font-medium" placeholder="Strip">
                                        <datalist id="sec-units-list">
                                            @foreach($availableUnits as $unit)
                                                <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] text-slate-600 font-medium mb-0.5">
                                            1 {{ $secondary_unit ?: 'Strip' }} contains
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <input type="number" min="1" wire:model.live="secondary_unit_to_base" class="w-24 h-8 px-2.5 border border-blue-300 rounded-md text-sm bg-white font-bold text-blue-900">
                                            <span class="text-xs font-semibold text-slate-700">{{ $base_unit ?: 'Tablets' }}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Purchase Price</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rs.</span>
                                                <input type="number" min="0" step="0.01" wire:model="secondary_unit_purchase_price" class="w-full h-8 pl-8 pr-2 border border-slate-300 rounded-md text-xs bg-white" placeholder="e.g. 80">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Sale Price</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rs.</span>
                                                <input type="number" min="0" step="0.01" wire:model="secondary_unit_selling_price" class="w-full h-8 pl-8 pr-2 border border-slate-300 rounded-md text-xs bg-white font-bold text-blue-700" placeholder="e.g. 110">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Strip Barcode (Optional)</label>
                                        <input type="text" wire:model="secondary_unit_barcode" class="w-full h-8 px-2 border border-slate-300 rounded-md text-xs bg-white" placeholder="Scan Strip barcode">
                                    </div>
                                </div>
                            </div>

                            {{-- 3. PRIMARY UNIT --}}
                            <div class="bg-indigo-50/40 p-4 rounded-xl border border-indigo-200 relative shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-layer-group text-indigo-500 text-[10px]"></i> Primary Unit (Level 3)
                                    </span>
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] font-bold rounded-md">
                                        = {{ $this->calculatedPrimaryConversion }} {{ $base_unit ?: 'Tablets' }}
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Primary Unit (e.g. Pack / Box)</label>
                                        <input type="text" list="prim-units-list" wire:model.live="primary_unit" class="w-full h-9 px-3 border border-indigo-200 rounded-lg text-sm bg-white font-medium" placeholder="Pack">
                                        <datalist id="prim-units-list">
                                            @foreach($availableUnits as $unit)
                                                <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] text-slate-600 font-medium mb-0.5">
                                            1 {{ $primary_unit ?: 'Pack' }} contains
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <input type="number" min="1" wire:model.live="primary_unit_to_secondary" class="w-24 h-8 px-2.5 border border-indigo-300 rounded-md text-sm bg-white font-bold text-indigo-900">
                                            <span class="text-xs font-semibold text-slate-700">{{ $secondary_unit ?: ($base_unit ?: 'Units') }}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Purchase Price</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rs.</span>
                                                <input type="number" min="0" step="0.01" wire:model="primary_unit_purchase_price" class="w-full h-8 pl-8 pr-2 border border-slate-300 rounded-md text-xs bg-white" placeholder="e.g. 800">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Sale Price</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rs.</span>
                                                <input type="number" min="0" step="0.01" wire:model="primary_unit_selling_price" class="w-full h-8 pl-8 pr-2 border border-slate-300 rounded-md text-xs bg-white font-bold text-indigo-700" placeholder="e.g. 1000">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] text-slate-600 font-medium mb-0.5">Pack Barcode (Optional)</label>
                                        <input type="text" wire:model="primary_unit_barcode" class="w-full h-8 px-2 border border-slate-300 rounded-md text-xs bg-white" placeholder="Scan Pack barcode">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PACKAGING PREVIEW COMPONENT --}}
                        <div class="mt-4 p-4 bg-linear-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200 rounded-xl shadow-xs">
                            <div class="flex items-center justify-between flex-wrap gap-2 mb-3 border-b border-emerald-200/60 pb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center gap-2">
                                    <i class="fa-solid fa-diagram-project text-emerald-600"></i>
                                    PACKAGING PREVIEW & AUTOMATIC CONVERSION
                                </span>
                                <span class="text-[11px] font-semibold text-emerald-700 bg-white px-2.5 py-0.5 rounded-full border border-emerald-200 shadow-2xs">
                                    Hierarchy Auto-Calculation Active
                                </span>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-center">
                                {{-- Visual Cascade Flow --}}
                                <div class="flex items-center justify-center gap-3 py-2 bg-white/80 rounded-lg border border-emerald-100 px-4">
                                    @if(!empty($primary_unit))
                                        <div class="text-center">
                                            <span class="block text-xs font-bold text-indigo-900 bg-indigo-100 px-3 py-1 rounded-md border border-indigo-200">
                                                1 {{ $primary_unit }}
                                            </span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right text-emerald-500 text-sm"></i>
                                    @endif

                                    @if(!empty($secondary_unit))
                                        <div class="text-center">
                                            <span class="block text-xs font-bold text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-200">
                                                {{ max(1, (int)$primary_unit_to_secondary) }} {{ Str::plural($secondary_unit, (int)$primary_unit_to_secondary) }}
                                            </span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right text-emerald-500 text-sm"></i>
                                    @endif

                                    <div class="text-center">
                                        <span class="block text-xs font-extrabold text-emerald-900 bg-emerald-100 px-3 py-1 rounded-md border border-emerald-300">
                                            {{ $this->calculatedPrimaryConversion }} {{ Str::plural($base_unit ?: 'Tablet', $this->calculatedPrimaryConversion) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Formula Summary --}}
                                <div class="text-xs space-y-1 bg-white/80 p-3 rounded-lg border border-emerald-100">
                                    @if(!empty($primary_unit))
                                        <div class="flex items-center justify-between text-slate-700 font-medium">
                                            <span>1 {{ $primary_unit }} =</span>
                                            <span class="font-bold text-indigo-800">{{ $this->calculatedPrimaryConversion }} {{ Str::plural($base_unit ?: 'Tablet', $this->calculatedPrimaryConversion) }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($secondary_unit))
                                        <div class="flex items-center justify-between text-slate-700 font-medium">
                                            <span>1 {{ $secondary_unit }} =</span>
                                            <span class="font-bold text-blue-800">{{ $this->calculatedSecondaryConversion }} {{ Str::plural($base_unit ?: 'Tablet', $this->calculatedSecondaryConversion) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between text-slate-700 font-medium">
                                        <span>1 {{ $base_unit ?: 'Tablet' }} =</span>
                                        <span class="font-bold text-emerald-800">1 Base Unit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ========================================================= --}}
                {{-- PRODUCT UNITS & PACKAGING (FOR GENERAL STORE ITEMS) --}}
                {{-- ========================================================= --}}
                @if($product_type === 'general')
                    <div class="mt-6 pt-5 border-t border-slate-200">
                        <h3 class="text-base font-bold text-slate-900 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-box text-blue-600"></i>
                            General Item Packaging & Units
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Outer / Purchase Packaging (Optional, e.g. Carton, Box)
                                </label>
                                <input type="text" list="gen-prim-units" wire:model.live="primary_unit" class="w-full h-9 px-3 border border-slate-200 rounded-lg text-sm bg-white" placeholder="Carton / Box (Leave empty if sold individually)">
                                <datalist id="gen-prim-units">
                                    <option value="Carton">
                                    <option value="Box">
                                    <option value="Pack">
                                    <option value="Dozen">
                                </datalist>

                                <div class="mt-2">
                                    <label class="block text-[11px] text-slate-500 mb-0.5">1 {{ $primary_unit ?: 'Outer Unit' }} contains</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="number" min="1" wire:model.live="primary_unit_to_secondary" class="w-24 h-8 px-2 border border-slate-200 rounded-md text-sm bg-white">
                                        <span class="text-xs text-slate-600 font-medium">{{ $base_unit ?: 'Pieces' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Base Sale Unit (e.g. Piece, Bottle, Liter, Kg) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" list="gen-base-units" wire:model.live="base_unit" class="w-full h-9 px-3 border border-slate-200 rounded-lg text-sm bg-white" placeholder="Piece">
                                <datalist id="gen-base-units">
                                    <option value="Piece">
                                    <option value="Bottle">
                                    <option value="Can">
                                    <option value="Kg">
                                    <option value="Liter">
                                    <option value="Packet">
                                </datalist>
                                <p class="text-[11px] text-slate-500 mt-1">Single item sale unit.</p>
                            </div>
                        </div>
                    </div>
                @endif



                {{-- Buttons --}}
                <div class="flex justify-end gap-3 mt-5">

                    <button
                        type="button"
                        wire:click="$refresh"
                        class="h-10 px-5 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-200"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="h-10 px-6 rounded-lg bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="save">
                            @if($product_type === 'general')
                                Save General Store Item
                            @else
                                Save Medicine
                            @endif
                        </span>

                        <span wire:loading wire:target="save">
                            Saving...
                        </span>
                    </button>

                </div>

            </form>

        </section>


        {{-- ========================================================= --}}
        {{-- STOCK LIST --}}
        {{-- ========================================================= --}}

        <section class="bg-white rounded-xl border border-slate-200 shadow-sm">

            {{-- Title --}}
            <div class="px-5 py-4 border-b border-slate-100">

                <div class="flex items-center gap-3">

                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10L4 17V7m8 14V11m0 0L4 7m8 4l8-4"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Products & Inventory List
                        </h2>

                        <p class="text-xs text-slate-500">
                            Current medicine and general store inventory stock
                        </p>
                    </div>

                </div>

            </div>


            {{-- Statistics --}}
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Total Products --}}
                <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10L4 17V7m8 14V11m0 0L4 7m8 4l8-4"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">
                                Total Products
                            </p>

                            <p class="text-2xl font-bold text-emerald-700">
                                {{ number_format($totalMedicines) }}
                            </p>
                        </div>

                    </div>

                </div>


                {{-- Total Stock --}}
                <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-4">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10L4 17V7m8 14V11m0 0L4 7m8 4l8-4"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">
                                Total Stock Units
                            </p>

                            <p class="text-2xl font-bold text-blue-700">
                                {{ number_format($totalStock) }}
                            </p>
                        </div>

                    </div>

                </div>


                {{-- Low Stock --}}
                <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">
                                Low Stock
                            </p>

                            <p class="text-2xl font-bold text-amber-700">
                                {{ number_format($lowStock) }}
                            </p>
                        </div>

                    </div>

                </div>


                {{-- Expired --}}
                <div class="rounded-xl border border-red-100 bg-red-50/50 p-4">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 9v4m0 4h.01M10.29 3.86l-7.7 13.33A2 2 0 004.32 20h15.36a2 2 0 001.73-2.81l-7.7-13.33a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">
                                Expired
                            </p>

                            <p class="text-2xl font-bold text-red-700">
                                {{ number_format($expired) }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>



            {{-- Filters --}}
            <div class="px-4 pb-4">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">

                    {{-- Search --}}
                    <div class="relative lg:col-span-1">

                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"/>
                        </svg>

                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search products..."
                            class="w-full h-10 pl-9 pr-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                        >

                    </div>


                    {{-- Product Type Filter --}}
                    <select
                        wire:model.live="productTypeFilter"
                        class="h-10 px-3 border border-slate-200 rounded-lg bg-white text-sm text-slate-600 font-medium"
                    >
                        <option value="all">All Product Types</option>
                        <option value="medicine">Medicines</option>
                        <option value="general">General Store</option>
                    </select>

                    {{-- Category --}}
                    <select
                        wire:model.live="categoryFilter"
                        class="h-10 px-3 border border-slate-200 rounded-lg bg-white text-sm text-slate-600"
                    >
                        <option value="">All Categories</option>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Supplier --}}
                    <select
                        wire:model.live="supplierFilter"
                        class="h-10 px-3 border border-slate-200 rounded-lg bg-white text-sm text-slate-600"
                    >
                        <option value="">All Suppliers</option>

                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>


                    {{-- Stock --}}
                    <select
                        wire:model.live="stockFilter"
                        class="h-10 px-3 border border-slate-200 rounded-lg bg-white text-sm text-slate-600"
                    >
                        <option value="">Stock Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                        <option value="expired">Expired</option>
                    </select>


                    {{-- Reset --}}
                    <button
                        type="button"
                        wire:click="$set('search', ''); $set('productTypeFilter', 'all'); $set('categoryFilter', ''); $set('supplierFilter', ''); $set('stockFilter', '');"
                        class="h-10 px-4 rounded-lg bg-slate-100 border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Reset Filters
                    </button>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- TABLE --}}
            {{-- ===================================================== --}}

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-100 text-slate-600">

                            <th class="px-4 py-3 text-left font-semibold">
                                #
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Type
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Product Name
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Category
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Batch Details
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Total Stock
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Selling Price
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Expiry Date
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Status
                            </th>

                        </tr>
                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($medicines as $index => $medicine)

                            @php
                                $stock = $medicine->batches->sum('quantity');

                                $alert = $medicine->alert_quantity ?? 10;

                                $activeExpiredBatch = $medicine->batches
                                    ->where('quantity', '>', 0)
                                    ->first(function ($batch) {
                                        return $batch->expiry_date &&
                                            $batch->expiry_date->isPast();
                                    });

                                $nearestBatch = $medicine->batches
                                    ->sortBy('expiry_date')
                                    ->first();

                                $displayBatch = $nearestBatch;

                                if ($activeExpiredBatch) {
                                    $displayBatch = $activeExpiredBatch;
                                }

                                $status = 'In Stock';
                                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';

                                if ($activeExpiredBatch) {
                                    $status = 'Expired';
                                    $statusClass = 'bg-red-50 text-red-700 border-red-200';
                                } elseif ($stock <= 0) {
                                    $status = 'Out of Stock';
                                    $statusClass = 'bg-red-50 text-red-700 border-red-200';
                                } elseif ($stock <= $alert) {
                                    $status = 'Low Stock';
                                    $statusClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                }
                            @endphp

                            <tr class="hover:bg-slate-50/70 transition">

                                <td class="px-4 py-4 text-slate-500">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-4 py-4">
                                    @if($medicine->is_general)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            <i class="fa-solid fa-store text-[10px]"></i> General
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="fa-solid fa-capsules text-[10px]"></i> Medicine
                                        </span>
                                    @endif
                                </td>


                                <td class="px-4 py-4">

                                    <div class="font-semibold text-slate-800">
                                        {{ $medicine->name }}
                                    </div>

                                    @if($medicine->generic_name)
                                        <div class="text-xs text-slate-400 mt-0.5">
                                            {{ $medicine->generic_name }}
                                        </div>
                                    @endif

                                </td>


                                <td class="px-4 py-4 text-slate-600">
                                    {{ $medicine->category?->name ?? '—' }}
                                </td>


                                <td class="px-4 py-4">

                                    @if($displayBatch)

                                        <div class="font-medium text-slate-700">
                                            {{ $displayBatch->batch_number }}
                                        </div>

                                        @if($displayBatch->expiry_date)
                                            <div class="text-xs text-slate-400">
                                                Exp:
                                                {{ $displayBatch->expiry_date->format('Y-m-d') }}
                                            </div>
                                        @endif

                                        @if($displayBatch->supplier)
                                            <div class="text-xs text-slate-400">
                                                {{ $displayBatch->supplier->name }}
                                            </div>
                                        @endif

                                    @else

                                        <span class="text-slate-400">
                                            No batch
                                        </span>

                                    @endif

                                </td>


                                <td class="px-4 py-4">
                                    <div
                                        @class([
                                            'font-bold text-sm',
                                            'text-emerald-600' => $stock > $alert,
                                            'text-amber-600' => $stock > 0 && $stock <= $alert,
                                            'text-red-600' => $stock <= 0,
                                        ])
                                    >
                                        {{ number_format($stock) }} <span class="text-xs font-normal text-slate-500">{{ $medicine->base_unit ?: 'Base Units' }}</span>
                                    </div>
                                    @if($stock > 0 && $medicine->packagings->count() > 1)
                                        <div class="text-[11px] font-medium text-slate-500 mt-0.5 max-w-xs">
                                            {{ $medicine->formatStockInUnits($stock) }}
                                        </div>
                                    @endif
                                </td>


                                <td class="px-4 py-4 text-slate-600">

                                    @if($displayBatch)
                                        PKR {{ number_format($displayBatch->selling_price, 2) }}
                                    @else
                                        —
                                    @endif

                                </td>


                                <td class="px-4 py-4 text-slate-600">

                                    @if($displayBatch?->expiry_date)
                                        {{ $displayBatch->expiry_date->format('Y-m-d') }}
                                    @else
                                        —
                                    @endif

                                </td>


                                <td class="px-4 py-4">

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md border text-xs font-semibold {{ $statusClass }}">
                                        {{ $status }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">

                                    <div class="text-slate-400">
                                        No medicines found.
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Footer --}}
            <div class="px-4 py-4 border-t border-slate-100 text-sm text-slate-500">
                Showing
                <span class="font-semibold text-slate-700">
                    {{ $medicines->count() }}
                </span>
                medicine entries
            </div>

        </section>

    {{-- ============================================================= --}}
    {{-- AUTO-SUGGESTION DATALISTS --}}
    {{-- ============================================================= --}}
    <datalist id="brand-suggestions">
        @foreach($suggestedBrands as $b)
            <option value="{{ $b }}">
        @endforeach
    </datalist>

    <datalist id="generic-suggestions">
        @foreach($suggestedGenerics as $g)
            <option value="{{ $g }}">
        @endforeach
    </datalist>

    <datalist id="manufacturer-suggestions">
        @foreach($suggestedManufacturers as $m)
            <option value="{{ $m }}">
        @endforeach
    </datalist>

    <datalist id="dosage-form-suggestions">
        @foreach($suggestedDosageForms as $df)
            <option value="{{ $df }}">
        @endforeach
    </datalist>

    <datalist id="strength-suggestions">
        @foreach($suggestedStrengths as $st)
            <option value="{{ $st }}">
        @endforeach
    </datalist>

    {{-- ============================================================= --}}
    {{-- LOCAL MEDICINE SEARCH API --}}
    {{-- ============================================================= --}}

    @script
    <script>
        (() => {

            const input = document.getElementById('medicine-search');
            const box = document.getElementById('medicine-suggestions');

            if (!input || !box) {
                return;
            }

            let timer = null;

            input.addEventListener('input', function () {

                clearTimeout(timer);

                const query = this.value.trim();

                if (query.length < 1) {
                    box.innerHTML = '';
                    box.classList.add('hidden');
                    return;
                }

                timer = setTimeout(async () => {

                    try {

                        const response = await fetch(
                            `/api/medicines/search?q=${encodeURIComponent(query)}`,
                            {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );

                        if (!response.ok) {
                            throw new Error('API request failed');
                        }

                        const medicines = await response.json();

                        box.innerHTML = '';

                        if (!medicines.length) {
                            box.innerHTML = `
                                <div class="px-4 py-3 text-sm text-slate-500">
                                    No medicine found
                                </div>
                            `;

                            box.classList.remove('hidden');
                            return;
                        }

                        medicines.forEach(medicine => {

                            const button = document.createElement('button');

                            button.type = 'button';

                            button.className =
                                'w-full text-left px-4 py-3 hover:bg-emerald-50 border-b border-slate-100 last:border-0';

                            button.innerHTML = `
                                <div class="font-medium text-slate-800">
                                    ${escapeHtml(medicine.name)}
                                </div>

                                <div class="text-xs text-slate-400 mt-0.5">
                                    ${escapeHtml(medicine.generic_name ?? '')}
                                    ${medicine.brand ? ' • ' + escapeHtml(medicine.brand) : ''}
                                </div>
                            `;

                            button.addEventListener('click', () => {

                                input.value = medicine.name;

                                if (window.Livewire) {
                                    const component = input.closest('[wire\\:id]');

                                    if (component) {
                                        Livewire.find(component.getAttribute('wire:id'))
                                            .set('name', medicine.name);
                                    }
                                }

                                box.innerHTML = '';
                                box.classList.add('hidden');
                            });

                            box.appendChild(button);

                        });

                        box.classList.remove('hidden');

                    } catch (error) {

                        console.error('Medicine API error:', error);

                        box.innerHTML = `
                            <div class="px-4 py-3 text-sm text-red-500">
                                Unable to search medicines
                            </div>
                        `;

                        box.classList.remove('hidden');
                    }

                }, 250);

            });


            document.addEventListener('click', function (event) {

                if (!input.contains(event.target) &&
                    !box.contains(event.target)) {

                    box.classList.add('hidden');
                }

            });


            function escapeHtml(value) {

                const div = document.createElement('div');

                div.textContent = value ?? '';

                return div.innerHTML;
            }

        })();
    </script>
    @endscript

</div>