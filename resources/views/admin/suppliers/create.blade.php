@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Banner (Matched with Purchase & Index pages) -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Supplier</h1>
            <p class="text-sm text-gray-500 mt-1">Register pharmaceutical vendors, contact details, and initial payable balances.</p>
        </div>
        <div>
            <a href="/suppliers" class="bg-slate-800 text-white px-4 py-2.5 rounded-lg font-bold text-sm hover:bg-slate-900 shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Suppliers</span>
            </a>
        </div>
    </div>

    <!-- Supplier Form Card -->
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <form action="/suppliers" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Company / Supplier Name *</label>
                    <input type="text" name="name" placeholder="e.g. Pharma Distributors" class="w-full p-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-600 transition" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="e.g. Muhammad Ali" class="w-full p-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Phone Number *</label>
                    <input type="text" name="phone" placeholder="e.g. 0300-1234567" class="w-full p-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-600 transition" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Email Address</label>
                    <input type="email" name="email" placeholder="e.g. info@distributor.com" class="w-full p-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">GST Tax Number</label>
                    <input type="text" name="gst_number" placeholder="e.g. GST-998877" class="w-full p-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Opening Balance / Payable (PKR)</label>
                    <input type="number" step="0.01" name="opening_balance" value="0" class="w-full p-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-600 transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Address</label>
                    <textarea name="address" rows="3" placeholder="Enter full business address..." class="w-full p-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-600 transition"></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="/suppliers" class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-300 transition">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-700 shadow transition">Save Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection