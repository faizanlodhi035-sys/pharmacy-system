@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

        <div class="flex items-center gap-3">

            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200
                        flex items-center justify-center text-blue-600">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>

            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Sales Return
                </h1>

                <p class="text-sm text-gray-500 mt-0.5">
                    Process a customer medicine return
                </p>
            </div>

        </div>

        <a href="{{ route('returns.index') }}"
           class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5 rounded-lg border border-gray-300
                  bg-white text-gray-700 text-sm font-semibold
                  hover:bg-gray-50 transition">

            ← Back to Returns

        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="flex items-center gap-3 px-4 py-3 rounded-xl
                    bg-green-50 border border-green-200 text-green-700">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M5 13l4 4L19 7"/>

            </svg>

            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR MESSAGE --}}
    @if(session('error'))

        <div class="px-4 py-3 rounded-xl
                    bg-red-50 border border-red-200 text-red-700">

            {{ session('error') }}

        </div>

    @endif


    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="px-4 py-3 rounded-xl
                    bg-red-50 border border-red-200">

            <ul class="list-disc list-inside text-sm text-red-600">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- FIND SALE --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-visible">

        <div class="px-5 py-4 bg-blue-50 border-b border-blue-100">

            <h2 class="font-bold text-gray-900">
                Find Sale Invoice
            </h2>

            <p class="text-xs text-gray-500">
                Select an existing sale invoice to process its return.
            </p>

        </div>


        <div class="p-5">

            <form method="GET"
                  action="{{ route('returns.sales.create') }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    <div class="md:col-span-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Sale Invoice
                        </label>

                        <select name="sale_id"
                                class="w-full h-11 px-3 rounded-lg border border-gray-300
                                       bg-white text-sm text-gray-700
                                       focus:border-blue-500 focus:ring-2
                                       focus:ring-blue-100 outline-none">

                            <option value="">
                                Select Sale Invoice
                            </option>

                            @foreach($sales as $sale)

                                <option value="{{ $sale->id }}"
                                    @selected($selectedSale?->id == $sale->id)>

                                    {{ $sale->invoice_number }}
                                    —
                                    {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                                    —
                                    PKR {{ number_format($sale->total_amount, 2) }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <button type="submit"
                                class="w-full h-11 rounded-lg
                                       bg-blue-600 text-white
                                       text-sm font-semibold
                                       hover:bg-blue-700 transition">

                            Find Invoice

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- SELECTED SALE --}}
    @if($selectedSale)

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            {{-- SALE HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center
                        md:justify-between gap-3
                        px-5 py-4 bg-blue-50 border-b border-blue-100">

                <div>

                    <h2 class="font-bold text-gray-900">
                        Sale Invoice Details
                    </h2>

                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $selectedSale->invoice_number }}
                    </p>

                </div>

                <div class="text-sm font-bold text-blue-700">
                    Original Sale:
                    PKR {{ number_format($selectedSale->total_amount, 2) }}
                </div>

            </div>


            {{-- RETURN FORM --}}
            <form method="POST"
                  action="{{ route('returns.sales.store') }}"
                  id="sales-return-form">

                @csrf

                <input type="hidden"
                       name="sale_id"
                       value="{{ $selectedSale->id }}">


                {{-- ITEMS TABLE --}}
                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1100px] text-sm">

                        <thead>

                            <tr class="bg-gray-50 border-b border-gray-200">

                                <th class="px-5 py-3 text-left">
                                    #
                                </th>

                                <th class="px-5 py-3 text-left">
                                    Medicine
                                </th>

                                <th class="px-5 py-3 text-left">
                                    Batch
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Sold Qty
                                </th>

                                <th class="px-5 py-3 text-right">
                                    Unit Price
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Return Qty
                                </th>

                                <th class="px-5 py-3 text-right">
                                    Refund Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @forelse($selectedSale->items as $item)

                                <tr class="hover:bg-blue-50/30 transition">

                                    {{-- # --}}
                                    <td class="px-5 py-4 text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>


                                    {{-- MEDICINE --}}
                                    <td class="px-5 py-4">

                                        <div class="font-semibold text-gray-800">

                                            {{ $item->medicine?->name ?? 'Unknown Medicine' }}

                                        </div>

                                        @if($item->medicine?->generic_name)

                                            <div class="text-xs text-gray-500 mt-0.5">
                                                {{ $item->medicine->generic_name }}
                                            </div>

                                        @endif

                                    </td>


                                    {{-- BATCH --}}
                                    <td class="px-5 py-4 text-gray-600">

                                        {{ $item->batch?->batch_number ?? 'N/A' }}

                                    </td>


                                    {{-- SOLD QTY --}}
                                    <td class="px-5 py-4 text-center font-semibold">

                                        {{ number_format($item->quantity, 0) }} <span class="text-xs font-bold text-blue-600">{{ $item->unit ?? '' }}</span>

                                    </td>


                                    {{-- UNIT PRICE --}}
                                    <td class="px-5 py-4 text-right">

                                        PKR {{ number_format($item->unit_price, 2) }}

                                    </td>


                                    {{-- RETURN QTY --}}
                                    <td class="px-5 py-4 text-center">

                                        <input
                                            type="number"
                                            name="items[{{ $item->id }}][quantity]"
                                            min="0"
                                            max="{{ $item->quantity }}"
                                            value="0"
                                            step="1"
                                            data-unit-price="{{ $item->unit_price }}"
                                            class="return-qty w-24 h-10 px-3 text-center
                                                   rounded-lg border border-gray-300
                                                   focus:border-blue-500
                                                   focus:ring-2 focus:ring-blue-100
                                                   outline-none">

                                        <input
                                            type="hidden"
                                            name="items[{{ $item->id }}][sale_item_id]"
                                            value="{{ $item->id }}">

                                        <p class="text-[11px] text-gray-400 mt-1">
                                            Max {{ $item->quantity }}
                                        </p>

                                    </td>


                                    {{-- REFUND AMOUNT --}}
                                    <td class="px-5 py-4 text-right">

                                        <span
                                            class="refund-amount font-bold text-blue-700"
                                            data-item-id="{{ $item->id }}">
                                            PKR 0.00
                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7"
                                        class="px-5 py-10 text-center text-gray-400">

                                        No items found for this sale.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- REFUND SUMMARY --}}
                @if($selectedSale->items->count())

                    <div class="px-5 py-5 bg-gray-50 border-t border-gray-200">

                        <div class="flex flex-col md:flex-row
                                    md:items-center md:justify-between gap-4">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Customer Refund
                                </p>

                                <p class="text-2xl font-bold text-blue-700">

                                    PKR
                                    <span id="total-refund">
                                        0.00
                                    </span>

                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    Refund is calculated from returned quantity × original sale price.
                                </p>

                            </div>


                            <button type="submit"
                                    id="process-return-button"
                                    class="inline-flex items-center justify-center
                                           px-6 py-2.5 rounded-lg
                                           bg-blue-600 text-white
                                           text-sm font-semibold
                                           hover:bg-blue-700
                                           disabled:opacity-50
                                           disabled:cursor-not-allowed
                                           transition">

                                Process Sales Return

                            </button>

                        </div>

                    </div>

                @endif

            </form>

        </div>


    @else

        {{-- EMPTY STATE --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            <div class="px-5 py-12 text-center">

                <div class="mx-auto w-12 h-12 rounded-full
                            bg-blue-50 flex items-center justify-center
                            text-blue-500 mb-3">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 14l6-6m-6 0l6 6m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>

                    </svg>

                </div>

                <h3 class="font-semibold text-gray-700">
                    Select a sale invoice
                </h3>

                <p class="text-sm text-gray-400 mt-1">
                    Choose a sale invoice above to view medicines
                    available for return.
                </p>

            </div>

        </div>

    @endif

</div>


{{-- ============================================================
     REFUND CALCULATION
============================================================ --}}
@if($selectedSale)

<script>
document.addEventListener('DOMContentLoaded', function () {

    function calculateRefund() {

        let totalRefund = 0;

        document.querySelectorAll('.return-qty').forEach(function (input) {

            let quantity = parseFloat(input.value) || 0;
            let unitPrice = parseFloat(input.dataset.unitPrice) || 0;

            let maxQuantity = parseFloat(input.max) || 0;

            // Prevent negative quantity
            if (quantity < 0) {
                quantity = 0;
                input.value = 0;
            }

            // Prevent returning more than sold
            if (quantity > maxQuantity) {
                quantity = maxQuantity;
                input.value = maxQuantity;
            }

            const refund = quantity * unitPrice;

            totalRefund += refund;

            const row = input.closest('tr');

            if (row) {

                const refundElement =
                    row.querySelector('.refund-amount');

                if (refundElement) {

                    refundElement.textContent =
                        'PKR ' + refund.toFixed(2);

                }

            }

        });


        const totalElement =
            document.getElementById('total-refund');

        if (totalElement) {

            totalElement.textContent =
                totalRefund.toFixed(2);

        }


        const button =
            document.getElementById('process-return-button');

        if (button) {

            button.disabled = totalRefund <= 0;

        }

    }


    document.querySelectorAll('.return-qty').forEach(function (input) {

        input.addEventListener('input', calculateRefund);
        input.addEventListener('change', calculateRefund);

    });


    calculateRefund();

});
</script>

@endif

@endsection