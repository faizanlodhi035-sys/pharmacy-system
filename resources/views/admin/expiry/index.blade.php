<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    <h2 class="text-xl font-bold mb-4 text-red-600">⚠️ Expiry Alerts (Next 30 Days)</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500">Medicine</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500">Batch</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500">Expiry Date</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expiringMedicines as $batch)
            <tr class="{{ $batch->expiry_date < now() ? 'bg-red-50' : 'bg-orange-50' }}">
                <td class="px-4 py-3">{{ $batch->medicine->name }}</td>
                <td class="px-4 py-3">{{ $batch->batch_number }}</td>
                <td class="px-4 py-3">{{ $batch->expiry_date }}</td>
                <td class="px-4 py-3 font-bold uppercase text-xs">
                    {{ $batch->expiry_date < now() ? 'EXPIRED' : 'EXPIRING SOON' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>