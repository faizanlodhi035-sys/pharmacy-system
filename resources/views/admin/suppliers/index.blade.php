@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Buttons -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Suppliers Management</h1>
            <p class="text-sm text-gray-500">Manage pharmaceutical vendors, contact details, and payable balances.</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Payable Report Button -->
            <a href="/suppliers-payable-report" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-purple-700 shadow flex items-center space-x-2">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Payable Report</span>
            </a>
            <!-- Add Supplier Button -->
            <a href="/suppliers/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 shadow flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add Supplier</span>
            </a>
        </div>
    </div>

    @if(session('message'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 text-sm font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Suppliers Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Company Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Contact Person</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Phone & Email</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">GST Number</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Payable Balance</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $supplier->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                <div><i class="fa-solid fa-phone mr-1"></i> {{ $supplier->phone }}</div>
                                <div class="text-gray-400"><i class="fa-solid fa-envelope mr-1"></i> {{ $supplier->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-700">{{ $supplier->gst_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-bold text-red-600">PKR {{ number_format($supplier->opening_balance, 2) }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <!-- History Button -->
                                <a href="/suppliers/{{ $supplier->id }}" class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-blue-700 shadow">History</a>
                                
                                <!-- Edit Button -->
                                <a href="/suppliers/{{ $supplier->id }}/edit" class="bg-amber-500 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-amber-600 shadow">Edit</a>
                                
                                <!-- Delete Button -->
                                <form action="/suppliers/{{ $supplier->id }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-red-700 shadow">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                No suppliers found. Click "Add Supplier" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection