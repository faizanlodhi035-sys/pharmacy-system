@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Accounts Payable Report</h1>
            <p class="text-sm text-gray-500">Track total outstanding balances and dues payable to suppliers.</p>
        </div>
        <div class="bg-red-50 border border-red-200 px-5 py-3 rounded-xl text-right">
            <span class="text-xs text-red-600 font-bold uppercase block">Total Payable Balance</span>
            <span class="text-2xl font-extrabold text-red-700">PKR {{ number_format($totalPayable, 2) }}</span>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Supplier Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Contact Person</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Phone</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">GST Number</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Payable Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $supplier->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $supplier->phone }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $supplier->gst_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-red-600">PKR {{ number_format($supplier->opening_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                No supplier records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <a href="/suppliers" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-300">Back to Suppliers</a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 shadow">Print Report</button>
        </div>
    </div>
</div>
@endsection