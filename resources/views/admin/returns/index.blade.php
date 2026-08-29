@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Returns Management
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Manage sales and purchase returns
            </p>
        </div>

    </div>


    {{-- RETURN MODULE CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- SALES RETURN --}}
        <a href="{{ route('returns.sales.create') }}"
           class="group bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition">

            <div class="flex items-start gap-4">

                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600
                            flex items-center justify-center
                            group-hover:bg-blue-100 transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 14l-3-3m0 0l3-3m-3 3h10a4 4 0 014 4v1"/>

                    </svg>

                </div>

                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Sales Return
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Return medicines from a customer sale.
                    </p>

                    <span class="inline-flex mt-4 text-sm font-semibold text-blue-600">
                        Create Sales Return →
                    </span>
                </div>

            </div>

        </a>


        {{-- PURCHASE RETURN --}}
        <a href="{{ route('returns.purchase.create') }}"
           class="group bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-green-300 transition">

            <div class="flex items-start gap-4">

                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600
                            flex items-center justify-center
                            group-hover:bg-green-100 transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M15 10l3 3m0 0l-3 3m3-3H8a4 4 0 01-4-4V8"/>

                    </svg>

                </div>

                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Purchase Return
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Return purchased medicines to a supplier.
                    </p>

                    <span class="inline-flex mt-4 text-sm font-semibold text-green-600">
                        Create Purchase Return →
                    </span>
                </div>

            </div>

        </a>

    </div>


    {{-- INFORMATION --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="font-bold text-gray-900">
                Return Module
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Select the appropriate return type to continue.
            </p>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
                <h3 class="font-semibold text-blue-800">
                    Sales Returns
                </h3>

                <p class="text-sm text-blue-700 mt-1">
                    Process customer medicine returns and update stock.
                </p>
            </div>

            <div class="rounded-lg border border-green-100 bg-green-50 p-4">
                <h3 class="font-semibold text-green-800">
                    Purchase Returns
                </h3>

                <p class="text-sm text-green-700 mt-1">
                    Process supplier returns and update purchase stock.
                </p>
            </div>

        </div>

    </div>

</div>

@endsection