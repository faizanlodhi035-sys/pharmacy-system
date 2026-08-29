@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-6">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ============================================================
             HEADER
        ============================================================ --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('purchases.show', $invoice->id) }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                               border border-slate-200 bg-white text-slate-600
                               shadow-sm transition hover:bg-slate-100"
                    >
                        ←
                    </a>

                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                            Edit Purchase
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Update purchase invoice and medicine information
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('purchases.show', $invoice->id) }}"
                    class="inline-flex items-center gap-2 rounded-xl
                           border border-slate-200 bg-white px-4 py-2.5
                           text-sm font-semibold text-slate-700 shadow-sm
                           transition hover:bg-slate-50"
                >
                    ← Back to Details
                </a>

            </div>
        </div>


        {{-- ============================================================
             VALIDATION ERRORS
        ============================================================ --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
                <div class="flex gap-3">
                    <div class="mt-0.5 text-red-600">
                        ⚠
                    </div>

                    <div>
                        <h3 class="font-semibold text-red-800">
                            Please fix the following errors:
                        </h3>

                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif


        {{-- ============================================================
             SUCCESS MESSAGE
        ============================================================ --}}
        @if (session('message'))
            <div
                class="mb-6 rounded-2xl border border-emerald-200
                       bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700"
            >
                ✓ {{ session('message') }}
            </div>
        @endif


        {{-- ============================================================
             FORM
        ============================================================ --}}
        <form
            method="POST"
            action="{{ route('purchases.update', $invoice->id) }}"
            id="purchaseEditForm"
        >

            @csrf
            @method('PUT')


            {{-- ========================================================
                 PURCHASE INFORMATION
            ======================================================== --}}
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 items-center justify-center
                                   rounded-xl bg-blue-100 text-blue-600"
                        >
                            🧾
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-slate-900">
                                Purchase Information
                            </h2>

                            <p class="text-sm text-slate-500">
                                Basic information of this purchase invoice
                            </p>
                        </div>

                    </div>
                </div>


                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-3">

                    {{-- Supplier --}}
                    <div>
                        <label
                            for="supplier_id"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Supplier
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="supplier_id"
                            name="supplier_id"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white
                                   px-4 py-3 text-sm text-slate-800 shadow-sm
                                   outline-none transition
                                   focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                            @php
                                $suppliersList = $suppliers ?? collect();
                            @endphp

                            @if ($suppliersList->count())
                                @foreach ($suppliersList as $supplier)
                                    <option
                                        value="{{ $supplier->id }}"
                                        @selected(
                                            old(
                                                'supplier_id',
                                                $invoice->supplier_id
                                            ) == $supplier->id
                                        )
                                    >
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="{{ $invoice->supplier_id }}" selected>
                                    {{ $invoice->supplier?->name ?? 'Current Supplier' }}
                                </option>
                            @endif

                        </select>

                        @error('supplier_id')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Invoice Number --}}
                    <div>
                        <label
                            for="invoice_number"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Invoice Number
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="invoice_number"
                            name="invoice_number"
                            value="{{ old('invoice_number', $invoice->invoice_number) }}"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 text-sm text-slate-800 shadow-sm
                                   outline-none transition
                                   focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('invoice_number')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Purchase Date --}}
                    <div>
                        <label
                            for="purchase_date"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Purchase Date
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="purchase_date"
                            name="purchase_date"
                            value="{{ old(
                                'purchase_date',
                                \Carbon\Carbon::parse($invoice->purchase_date)->format('Y-m-d')
                            ) }}"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 text-sm text-slate-800 shadow-sm
                                   outline-none transition
                                   focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('purchase_date')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>


            {{-- ========================================================
                 MEDICINE ITEMS
            ======================================================== --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 items-center justify-center
                                       rounded-xl bg-emerald-100 text-emerald-600"
                            >
                                💊
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-slate-900">
                                    Medicine Items
                                </h2>

                                <p class="text-sm text-slate-500">
                                    Update medicines, quantity, prices and expiry dates
                                </p>
                            </div>

                        </div>

                        <div
                            class="rounded-xl bg-blue-50 px-4 py-2 text-sm
                                   font-semibold text-blue-700"
                        >
                            {{ $invoice->items->count() }}
                            {{ $invoice->items->count() == 1 ? 'Item' : 'Items' }}
                        </div>

                    </div>
                </div>


                <div class="p-6">

                    @if ($invoice->items->count())

                        <div class="space-y-5" id="itemsContainer">

                            @foreach ($invoice->items as $index => $item)

                                <div
                                    class="item-row rounded-2xl border border-slate-200
                                           bg-slate-50 p-5"
                                    data-index="{{ $index }}"
                                >

                                    {{-- Item Header --}}
                                    <div class="mb-4 flex items-center justify-between">

                                        <div class="flex items-center gap-3">

                                            <div
                                                class="flex h-9 w-9 items-center justify-center
                                                       rounded-lg bg-white text-sm font-bold
                                                       text-slate-600 shadow-sm"
                                            >
                                                {{ $index + 1 }}
                                            </div>

                                            <div>
                                                <p class="text-sm font-bold text-slate-800">
                                                    Medicine Item
                                                </p>

                                                <p class="text-xs text-slate-500">
                                                    Batch:
                                                    {{ $item->batch_number }}
                                                </p>
                                            </div>

                                        </div>

                                    </div>


                                    {{-- Fields --}}
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">

                                        {{-- Medicine --}}
                                        <div class="lg:col-span-2">

                                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                                Medicine
                                            </label>

                                            <select
                                                name="items[{{ $index }}][medicine_id]"
                                                required
                                                class="w-full rounded-xl border border-slate-300
                                                       bg-white px-3 py-2.5 text-sm
                                                       text-slate-800 outline-none
                                                       focus:border-blue-500
                                                       focus:ring-4 focus:ring-blue-100"
                                            >

                                                @php
                                                    $medicineList = $medicines ?? collect();
                                                @endphp

                                                @if ($medicineList->count())

                                                    @foreach ($medicineList as $medicine)

                                                        <option
                                                            value="{{ $medicine->id }}"
                                                            @selected($item->medicine_id == $medicine->id)
                                                        >
                                                            {{ $medicine->name }}
                                                        </option>

                                                    @endforeach

                                                @else

                                                    <option
                                                        value="{{ $item->medicine_id }}"
                                                        selected
                                                    >
                                                        {{ $item->medicine?->name ?? 'Medicine' }}
                                                    </option>

                                                @endif

                                            </select>

                                        </div>


                                        {{-- Batch --}}
                                        <div>

                                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                                Batch Number
                                            </label>

                                            <input
                                                type="text"
                                                name="items[{{ $index }}][batch_number]"
                                                value="{{ old(
                                                    'items.' . $index . '.batch_number',
                                                    $item->batch_number
                                                ) }}"
                                                required
                                                class="w-full rounded-xl border border-slate-300
                                                       bg-white px-3 py-2.5 text-sm
                                                       text-slate-800 outline-none
                                                       focus:border-blue-500
                                                       focus:ring-4 focus:ring-blue-100"
                                            >

                                        </div>


                                        {{-- Quantity --}}
                                        <div>

                                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                                Quantity
                                            </label>

                                            <input
                                                type="number"
                                                name="items[{{ $index }}][quantity]"
                                                value="{{ old(
                                                    'items.' . $index . '.quantity',
                                                    $item->quantity
                                                ) }}"
                                                min="1"
                                                step="0.01"
                                                required
                                                class="w-full rounded-xl border border-slate-300
                                                       bg-white px-3 py-2.5 text-sm
                                                       text-slate-800 outline-none
                                                       focus:border-blue-500
                                                       focus:ring-4 focus:ring-blue-100
                                                       item-quantity"
                                            >

                                        </div>


                                        {{-- Purchase Price --}}
                                        <div>

                                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                                Purchase Price
                                            </label>

                                            <input
                                                type="number"
                                                name="items[{{ $index }}][purchase_price]"
                                                value="{{ old(
                                                    'items.' . $index . '.purchase_price',
                                                    $item->purchase_price
                                                ) }}"
                                                min="0"
                                                step="0.01"
                                                required
                                                class="w-full rounded-xl border border-slate-300
                                                       bg-white px-3 py-2.5 text-sm
                                                       text-slate-800 outline-none
                                                       focus:border-blue-500
                                                       focus:ring-4 focus:ring-blue-100
                                                       item-purchase-price"
                                            >

                                        </div>


                                        {{-- Selling Price --}}
                                        <div>

                                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                                Sale Price
                                            </label>

                                            <input
                                                type="number"
                                                name="items[{{ $index }}][selling_price]"
                                                value="{{ old(
                                                    'items.' . $index . '.selling_price',
                                                    $item->selling_price
                                                ) }}"
                                                min="0"
                                                step="0.01"
                                                required
                                                class="w-full rounded-xl border border-slate-300
                                                       bg-white px-3 py-2.5 text-sm
                                                       text-slate-800 outline-none
                                                       focus:border-blue-500
                                                       focus:ring-4 focus:ring-blue-100"
                                            >

                                        </div>


                                        {{-- Expiry --}}
                                        <div>

                                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                                Expiry Date
                                            </label>

                                            <input
                                                type="date"
                                                name="items[{{ $index }}][expiry_date]"
                                                value="{{ old(
                                                    'items.' . $index . '.expiry_date',
                                                    \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d')
                                                ) }}"
                                                required
                                                class="w-full rounded-xl border border-slate-300
                                                       bg-white px-3 py-2.5 text-sm
                                                       text-slate-800 outline-none
                                                       focus:border-blue-500
                                                       focus:ring-4 focus:ring-blue-100"
                                            >

                                        </div>


                                        {{-- Tax --}}
                                        <div>

                                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                                Tax %
                                            </label>

                                            <input
                                                type="number"
                                                name="items[{{ $index }}][tax_percent]"
                                                value="{{ old(
                                                    'items.' . $index . '.tax_percent',
                                                    $item->tax_percent ?? 0
                                                ) }}"
                                                min="0"
                                                step="0.01"
                                                class="w-full rounded-xl border border-slate-300
                                                       bg-white px-3 py-2.5 text-sm
                                                       text-slate-800 outline-none
                                                       focus:border-blue-500
                                                       focus:ring-4 focus:ring-blue-100"
                                            >

                                        </div>

                                    </div>


                                    {{-- Item Total --}}
                                    <div class="mt-4 flex justify-end">

                                        <div class="rounded-xl bg-white px-4 py-3 shadow-sm">

                                            <span class="text-xs font-medium text-slate-500">
                                                Item Total
                                            </span>

                                            <span class="ml-3 text-base font-bold text-slate-900">
                                                Rs.
                                                {{ number_format((float) $item->total, 2) }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>


                        {{-- ====================================================
                             SUMMARY
                        ==================================================== --}}
                        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <p class="text-sm text-slate-500">
                                    Subtotal
                                </p>

                                <p class="mt-1 text-2xl font-bold text-slate-900">
                                    Rs.
                                    {{ number_format((float) $invoice->subtotal, 2) }}
                                </p>

                            </div>


                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <p class="text-sm text-slate-500">
                                    Tax
                                </p>

                                <p class="mt-1 text-2xl font-bold text-slate-900">
                                    Rs.
                                    {{ number_format((float) $invoice->tax_amount, 2) }}
                                </p>

                            </div>


                            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

                                <p class="text-sm font-medium text-blue-600">
                                    Grand Total
                                </p>

                                <p class="mt-1 text-2xl font-bold text-blue-900">
                                    Rs.
                                    {{ number_format((float) $invoice->grand_total, 2) }}
                                </p>

                            </div>

                        </div>


                    @else

                        <div class="rounded-2xl border border-dashed border-slate-300
                                    bg-slate-50 p-10 text-center">

                            <div class="text-4xl">
                                💊
                            </div>

                            <h3 class="mt-3 text-lg font-bold text-slate-800">
                                No Medicine Items
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                This purchase does not contain any medicine items.
                            </p>

                        </div>

                    @endif

                </div>
            </div>


            {{-- ============================================================
                 ACTIONS
            ============================================================ --}}
            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('purchases.show', $invoice->id) }}"
                    class="inline-flex items-center justify-center rounded-xl
                           border border-slate-300 bg-white px-6 py-3
                           text-sm font-semibold text-slate-700 shadow-sm
                           transition hover:bg-slate-50"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    id="updatePurchaseBtn"
                    class="inline-flex items-center justify-center gap-2
                           rounded-xl bg-blue-600 px-7 py-3
                           text-sm font-bold text-white shadow-sm
                           transition hover:bg-blue-700
                           focus:outline-none focus:ring-4 focus:ring-blue-200"
                >
                    <span>✓</span>
                    Update Purchase
                </button>

            </div>

        </form>

    </div>
</div>


{{-- ================================================================
     FORM SUBMIT PROTECTION
================================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('purchaseEditForm');
    const button = document.getElementById('updatePurchaseBtn');

    if (!form || !button) {
        return;
    }

    form.addEventListener('submit', function () {

        button.disabled = true;

        button.classList.add('opacity-70', 'cursor-not-allowed');

        button.innerHTML = `
            <svg
                class="h-4 w-4 animate-spin"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                ></path>
            </svg>

            Updating...
        `;
    });

});
</script>
@endsection