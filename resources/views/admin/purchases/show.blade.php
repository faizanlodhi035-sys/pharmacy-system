@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>

            <div class="flex items-center gap-3">

                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200
                            flex items-center justify-center text-blue-600">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>

                    </svg>

                </div>

                <div>

                    <h1 class="text-2xl font-bold text-gray-900">
                        Purchase Details
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        View complete purchase invoice and medicine details
                    </p>

                </div>

            </div>

        </div>


        {{-- ACTIONS --}}

        <div class="flex flex-wrap items-center gap-2">

            <a
                href="{{ route('purchases.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5
                       rounded-lg border border-gray-300 bg-white
                       text-gray-700 text-sm font-semibold
                       hover:bg-gray-50 transition"
            >

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 19l-7-7 7-7"/>

                </svg>

                Back

            </a>


            <a
                href="{{ route('purchases.print', $invoice->id) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5
                       rounded-lg border border-gray-300 bg-white
                       text-gray-700 text-sm font-semibold
                       hover:bg-gray-50 transition"
            >

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/>

                </svg>

                Print

            </a>


            <a
                href="{{ route('purchases.pdf', $invoice->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5
                       rounded-lg bg-red-600 text-white
                       text-sm font-semibold
                       hover:bg-red-700 transition"
            >

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 10v6m0 0l-3-3m3 3l3-3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2h-5l-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>

                </svg>

                PDF

            </a>


            <a
                href="{{ route('purchases.edit', $invoice->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5
                       rounded-lg bg-blue-600 text-white
                       text-sm font-semibold
                       hover:bg-blue-700 transition"
            >

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-7.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 7.5-7.5z"/>

                </svg>

                Edit

            </a>

        </div>

    </div>


    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}

    @if(session('message'))

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
                {{ session('message') }}
            </span>

        </div>

    @endif


    {{-- =========================================================
         INVOICE SUMMARY CARDS
    ========================================================== --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Invoice --}}

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Invoice Number
                    </p>

                    <p class="mt-2 text-lg font-bold text-gray-900">
                        {{ $invoice->invoice_number }}
                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-blue-50
                            flex items-center justify-center text-blue-600">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>

                    </svg>

                </div>

            </div>

        </div>


        {{-- Supplier --}}

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Supplier
                    </p>

                    <p class="mt-2 text-lg font-bold text-gray-900">
                        {{ $invoice->supplier?->name ?? 'N/A' }}
                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-purple-50
                            flex items-center justify-center text-purple-600">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M17 20h5V4H2v16h5m10 0v-5H7v5m10 0H7"/>

                    </svg>

                </div>

            </div>

        </div>


        {{-- Date --}}

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Purchase Date
                    </p>

                    <p class="mt-2 text-lg font-bold text-gray-900">

                        {{ \Carbon\Carbon::parse($invoice->purchase_date)->format('d M Y') }}

                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-green-50
                            flex items-center justify-center text-green-600">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>

                    </svg>

                </div>

            </div>

        </div>


        {{-- Grand Total --}}

        <div class="bg-blue-600 rounded-xl p-5 shadow-sm text-white">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-100">
                        Grand Total
                    </p>

                    <p class="mt-2 text-2xl font-bold">
                        PKR {{ number_format($invoice->grand_total, 2) }}
                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-white/15
                            flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 8c-2.21 0-4 1.12-4 2.5S9.79 13 12 13s4 1.12 4 2.5S14.21 18 12 18m0-10V6m0 12v-2m0 0c-2.21 0-4-1.12-4-2.5M12 16c2.21 0 4-1.12 4-2.5"/>

                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         SUPPLIER + INVOICE INFORMATION
    ========================================================== --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Supplier Information --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

                <h2 class="font-bold text-gray-900">
                    Supplier Information
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Supplier details for this purchase
                </p>

            </div>

            <div class="p-5 space-y-4">

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Supplier Name
                    </span>

                    <span class="text-sm font-semibold text-gray-900 text-right">
                        {{ $invoice->supplier?->name ?? 'N/A' }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Contact Person
                    </span>

                    <span class="text-sm font-semibold text-gray-900 text-right">
                        {{ $invoice->supplier?->contact_person ?? 'N/A' }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Phone
                    </span>

                    <span class="text-sm font-semibold text-gray-900 text-right">
                        {{ $invoice->supplier?->phone ?? 'N/A' }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Email
                    </span>

                    <span class="text-sm font-semibold text-gray-900 text-right break-all">
                        {{ $invoice->supplier?->email ?? 'N/A' }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Address
                    </span>

                    <span class="text-sm font-semibold text-gray-900 text-right">
                        {{ $invoice->supplier?->address ?? 'N/A' }}
                    </span>

                </div>

            </div>

        </div>


        {{-- Invoice Information --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

                <h2 class="font-bold text-gray-900">
                    Invoice Information
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Purchase transaction summary
                </p>

            </div>

            <div class="p-5 space-y-4">

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Invoice Number
                    </span>

                    <span class="text-sm font-bold text-gray-900">
                        {{ $invoice->invoice_number }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Purchase Date
                    </span>

                    <span class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($invoice->purchase_date)->format('d/m/Y') }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Medicine Items
                    </span>

                    <span class="text-sm font-semibold text-gray-900">
                        {{ $invoice->items->count() }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Total Units
                    </span>

                    <span class="text-sm font-semibold text-gray-900">
                        {{ number_format($invoice->items->sum('quantity'), 0) }}
                    </span>

                </div>

                <div class="flex justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Created
                    </span>

                    <span class="text-sm font-semibold text-gray-900">

                        {{ $invoice->created_at?->format('d/m/Y h:i A') }}

                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MEDICINE ITEMS
    ========================================================== --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 bg-blue-50 border-b border-blue-100">

            <div class="flex items-center justify-between">

                <div>

                    <h2 class="font-bold text-gray-900">
                        Purchased Medicines
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Complete medicine and batch information
                    </p>

                </div>

                <div class="text-sm font-semibold text-blue-700">

                    {{ $invoice->items->count() }} item(s)

                </div>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-sm">

                <thead>

                    <tr class="bg-gray-50 border-b border-gray-200">

                        <th class="px-5 py-3 text-left font-semibold text-gray-700">
                            #
                        </th>

                        <th class="px-5 py-3 text-left font-semibold text-gray-700">
                            Medicine
                        </th>

                        <th class="px-5 py-3 text-left font-semibold text-gray-700">
                            Batch Number
                        </th>

                        <th class="px-5 py-3 text-center font-semibold text-gray-700">
                            Quantity
                        </th>

                        <th class="px-5 py-3 text-right font-semibold text-gray-700">
                            Purchase Price
                        </th>

                        <th class="px-5 py-3 text-right font-semibold text-gray-700">
                            Sale Price
                        </th>

                        <th class="px-5 py-3 text-center font-semibold text-gray-700">
                            Expiry
                        </th>

                        <th class="px-5 py-3 text-right font-semibold text-gray-700">
                            Total
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($invoice->items as $index => $item)

                        <tr class="hover:bg-blue-50/30 transition">

                            <td class="px-5 py-4 text-gray-500">
                                {{ $index + 1 }}
                            </td>


                            <td class="px-5 py-4">

                                <div class="font-semibold text-gray-900">

                                    {{ $item->medicine?->name ?? 'N/A' }}

                                </div>

                                @if($item->medicine?->generic_name)

                                    <div class="text-xs text-gray-500 mt-1">

                                        {{ $item->medicine->generic_name }}

                                    </div>

                                @endif

                            </td>


                            <td class="px-5 py-4">

                                <span class="inline-flex px-2.5 py-1
                                             rounded-md bg-gray-100
                                             text-gray-700 text-xs font-semibold">

                                    {{ $item->batch_number ?? 'N/A' }}

                                </span>

                            </td>


                            <td class="px-5 py-4 text-center font-semibold text-gray-900">

                                {{ number_format($item->quantity, 0) }}

                            </td>


                            <td class="px-5 py-4 text-right text-gray-700">

                                PKR {{ number_format($item->purchase_price, 2) }}

                            </td>


                            <td class="px-5 py-4 text-right text-gray-700">

                                PKR {{ number_format($item->selling_price, 2) }}

                            </td>


                            <td class="px-5 py-4 text-center">

                                @if($item->expiry_date)

                                    @php
                                        $expiry = \Carbon\Carbon::parse($item->expiry_date);
                                    @endphp

                                    <span class="
                                        inline-flex px-2.5 py-1 rounded-md text-xs font-semibold
                                        {{ $expiry->isPast()
                                            ? 'bg-red-50 text-red-700 border border-red-200'
                                            : 'bg-green-50 text-green-700 border border-green-200' }}
                                    ">

                                        {{ $expiry->format('d/m/Y') }}

                                    </span>

                                @else

                                    <span class="text-gray-400">
                                        N/A
                                    </span>

                                @endif

                            </td>


                            <td class="px-5 py-4 text-right font-bold text-gray-900">

                                PKR {{ number_format($item->total, 2) }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-5 py-12 text-center text-gray-400"
                            >

                                No medicine items found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================================================
         FINANCIAL SUMMARY
    ========================================================== --}}

    <div class="flex justify-end">

        <div class="w-full lg:w-[420px] bg-white border border-gray-200
                    rounded-xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

                <h2 class="font-bold text-gray-900">
                    Payment Summary
                </h2>

            </div>


            <div class="p-5 space-y-4">

                <div class="flex justify-between">

                    <span class="text-sm text-gray-500">
                        Subtotal
                    </span>

                    <span class="font-semibold text-gray-900">

                        PKR {{ number_format($invoice->subtotal, 2) }}

                    </span>

                </div>


                <div class="flex justify-between">

                    <span class="text-sm text-gray-500">
                        Tax
                    </span>

                    <span class="font-semibold text-gray-900">

                        PKR {{ number_format($invoice->tax_amount, 2) }}

                    </span>

                </div>


                <div class="border-t border-gray-200 pt-4">

                    <div class="flex justify-between items-center">

                        <span class="text-base font-bold text-gray-900">
                            Grand Total
                        </span>

                        <span class="text-xl font-bold text-blue-700">

                            PKR {{ number_format($invoice->grand_total, 2) }}

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         DANGER ZONE
    ========================================================== --}}

    <div class="bg-white border border-red-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 bg-red-50 border-b border-red-100">

            <h2 class="font-bold text-red-800">
                Purchase Actions
            </h2>

            <p class="text-xs text-red-600 mt-1">
                Deleting a purchase may affect stock and supplier balance.
            </p>

        </div>

        <div class="px-5 py-4 flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm font-semibold text-gray-800">
                    Delete this purchase
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    This action cannot be undone.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('purchases.destroy', $invoice->id) }}"
                onsubmit="return confirm('Are you sure you want to delete this purchase? This action cannot be undone.');"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="inline-flex items-center gap-2
                           px-4 py-2.5 rounded-lg
                           bg-red-600 text-white
                           text-sm font-semibold
                           hover:bg-red-700 transition"
                >

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 01-1-1h-4a1 1 0 01-1 1v3M4 7h16"/>

                    </svg>

                    Delete Purchase

                </button>

            </form>

        </div>

    </div>

</div>

@endsection