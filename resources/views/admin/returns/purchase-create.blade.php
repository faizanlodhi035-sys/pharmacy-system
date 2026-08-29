@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div class="flex items-center gap-3">

            <div class="w-11 h-11 rounded-xl bg-green-50 border border-green-200
                        flex items-center justify-center text-green-600">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M14 10l3-3m0 0l-3-3m3 3H7a4 4 0 00-4 4v1"/>
                </svg>

            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Purchase Return
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Return purchased medicines to supplier
                </p>
            </div>

        </div>


        <a href="{{ route('returns.index') }}"
           class="inline-flex items-center justify-center
                  px-4 py-2.5 rounded-lg
                  border border-gray-300 bg-white
                  text-sm font-semibold text-gray-700
                  hover:bg-gray-50">

            ← Back to Returns

        </a>

    </div>


    {{-- FIND PURCHASE --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <div class="px-5 py-4 bg-green-50 border-b border-green-100">

            <h2 class="font-bold text-gray-900">
                Find Purchase Invoice
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Select a purchase invoice to process a supplier return.
            </p>

        </div>


        <div class="p-5">

            <form method="GET"
                  action="{{ route('returns.purchase.create') }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    <div class="md:col-span-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Purchase Invoice
                        </label>

                        <select name="purchase_id"
                                class="w-full h-11 px-3 rounded-lg
                                       border border-gray-300
                                       bg-white text-sm
                                       focus:border-green-500
                                       focus:ring-2
                                       focus:ring-green-100
                                       outline-none">

                            <option value="">
                                Select Purchase Invoice
                            </option>

                            @forelse($purchases as $purchaseOption)

                                <option value="{{ $purchaseOption->id }}"
                                    @selected(isset($purchase) && $purchase->id == $purchaseOption->id)>

                                    {{ $purchaseOption->invoice_number }}

                                    —

                                    {{ $purchaseOption->supplier?->name ?? 'Unknown Supplier' }}

                                    —

                                    PKR {{ number_format($purchaseOption->grand_total ?? 0, 2) }}

                                </option>

                            @empty

                                <option value="">
                                    No purchase invoices found
                                </option>

                            @endforelse

                        </select>

                    </div>


                    <div>

                        <button type="submit"
                                class="w-full h-11 rounded-lg
                                       bg-green-600 text-white
                                       text-sm font-semibold
                                       hover:bg-green-700 transition">

                            Find Invoice

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- SELECTED PURCHASE --}}
    @if($purchase)

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            {{-- PURCHASE HEADER --}}
            <div class="px-5 py-4 bg-green-50 border-b border-green-100
                        flex flex-col md:flex-row md:items-center
                        md:justify-between gap-3">

                <div>

                    <h2 class="font-bold text-gray-900">
                        Purchase Invoice Details
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Invoice:
                        {{ $purchase->invoice_number }}
                    </p>

                    <p class="text-xs text-gray-500">
                        Supplier:
                        {{ $purchase->supplier?->name ?? 'Unknown Supplier' }}
                    </p>

                </div>


                <div class="text-lg font-bold text-green-700">

                    PKR {{ number_format($purchase->grand_total ?? 0, 2) }}

                </div>

            </div>


            {{-- RETURN FORM --}}
            <form method="POST"
                  action="{{ route('returns.purchase.store') }}">

                @csrf

                <input type="hidden"
                       name="purchase_invoice_id"
                       value="{{ $purchase->id }}">

                <input type="hidden"
                       name="return_date"
                       value="{{ now()->format('Y-m-d') }}">


                <div class="overflow-x-auto">

                    <table class="w-full min-w-[900px] text-sm">

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
                                    Purchased Qty
                                </th>

                                <th class="px-5 py-3 text-right">
                                    Purchase Price
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Return Qty
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @forelse($purchase->items as $item)

                                <tr class="hover:bg-green-50/30">

                                    <td class="px-5 py-4 text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>


                                    <td class="px-5 py-4">

                                        <div class="font-semibold text-gray-800">

                                            {{ $item->medicine?->name ?? 'Unknown Medicine' }}

                                        </div>

                                        @if($item->medicine?->category)

                                            <div class="text-xs text-gray-400 mt-1">

                                                {{ $item->medicine->category->name }}

                                            </div>

                                        @endif

                                    </td>


                                    <td class="px-5 py-4 text-gray-600">

                                        {{ $item->batch_number ?? 'N/A' }}

                                    </td>


                                    <td class="px-5 py-4 text-center font-semibold">

                                        {{ number_format($item->quantity, 0) }} <span class="text-xs font-bold text-blue-600">{{ $item->unit ?? '' }}</span>

                                    </td>


                                    <td class="px-5 py-4 text-right">

                                        PKR {{ number_format($item->purchase_price ?? 0, 2) }}

                                    </td>


                                    <td class="px-5 py-4 text-center">

                                        <input
                                            type="number"
                                            name="items[{{ $item->id }}][quantity]"
                                            min="0"
                                            max="{{ $item->quantity }}"
                                            value="0"
                                            step="1"
                                            class="w-24 h-10 px-3 text-center
                                                   rounded-lg border border-gray-300
                                                   focus:border-green-500
                                                   focus:ring-2
                                                   focus:ring-green-100
                                                   outline-none">

                                        <input
                                            type="hidden"
                                            name="items[{{ $item->id }}][purchase_invoice_item_id]"
                                            value="{{ $item->id }}">

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6"
                                        class="px-5 py-12 text-center text-gray-400">

                                        No items found in this purchase invoice.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- REASON --}}
                @if($purchase->items->count())

                    <div class="px-5 py-4 border-t border-gray-200">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Return Reason
                        </label>

                        <textarea
                            name="reason"
                            rows="3"
                            placeholder="Optional return reason..."
                            class="w-full rounded-lg border border-gray-300
                                   px-3 py-2 text-sm
                                   focus:border-green-500
                                   focus:ring-2
                                   focus:ring-green-100
                                   outline-none"></textarea>

                    </div>


                    {{-- BUTTON --}}
                    <div class="flex justify-end
                                px-5 py-4
                                bg-gray-50
                                border-t border-gray-200">

                        <button type="submit"
                                class="inline-flex items-center justify-center
                                       px-6 py-2.5 rounded-lg
                                       bg-green-600 text-white
                                       text-sm font-semibold
                                       hover:bg-green-700 transition">

                            Process Purchase Return

                        </button>

                    </div>

                @endif

            </form>

        </div>


    @else

        {{-- EMPTY STATE --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            <div class="px-5 py-14 text-center">

                <div class="mx-auto w-12 h-12 rounded-full
                            bg-green-50
                            flex items-center justify-center
                            text-green-500 mb-3">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M3 7h18M5 7l1 13h12l1-13M9 11v5m6-5v5"/>
                    </svg>

                </div>


                <h3 class="font-semibold text-gray-700">
                    Select a purchase invoice
                </h3>


                <p class="text-sm text-gray-400 mt-1">
                    Choose a purchase invoice above to view
                    medicines available for return.
                </p>

            </div>

        </div>

    @endif

</div>

@endsection