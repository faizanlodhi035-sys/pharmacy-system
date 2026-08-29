@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Edit Supplier Details</h2>
    
    <form action="/suppliers/{{ $supplier->id }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Company / Supplier Name *</label>
                <input type="text" name="name" value="{{ $supplier->name }}" class="w-full mt-1 p-2.5 border rounded-lg text-sm focus:ring focus:ring-blue-300" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full mt-1 p-2.5 border rounded-lg text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Phone Number *</label>
                <input type="text" name="phone" value="{{ $supplier->phone }}" class="w-full mt-1 p-2.5 border rounded-lg text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ $supplier->email }}" class="w-full mt-1 p-2.5 border rounded-lg text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">GST Tax Number</label>
                <input type="text" name="gst_number" value="{{ $supplier->gst_number }}" class="w-full mt-1 p-2.5 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Payable Balance (PKR)</label>
                <input type="number" step="0.01" name="opening_balance" value="{{ $supplier->opening_balance }}" class="w-full mt-1 p-2.5 border rounded-lg text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="address" rows="3" class="w-full mt-1 p-2.5 border rounded-lg text-sm">{{ $supplier->address }}</textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <a href="/suppliers" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-bold text-sm">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 shadow">Update Supplier</button>
        </div>
    </form>
</div>
@endsection