@extends('layouts.app')

@section('title', 'System Migration Utility')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto py-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Database Migration Utility</h1>
            <p class="text-sm text-gray-500 mt-1">Safely transfer catalog data from SQLite to PostgreSQL</p>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <div>
                <h3 class="text-sm font-bold">Success</h3>
                <p class="text-sm mt-1">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <h3 class="text-sm font-bold">Error</h3>
                <p class="text-sm mt-1">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- STATUS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Source: SQLite</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Database Path:</span>
                    <span class="font-mono text-xs {{ $sqliteExists ? 'text-gray-900' : 'text-red-500' }}">{{ $sqlitePath }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">File Status:</span>
                    @if($sqliteExists)
                        <span class="font-bold text-green-600">Found ({{ $sqliteSize }} MB)</span>
                    @else
                        <span class="font-bold text-red-600">Not Found</span>
                    @endif
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-100">
                    <span class="text-gray-600 font-semibold">Total Rows Across Tables:</span>
                    <span class="font-black text-gray-900">{{ number_format($totalSourceRows) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-semibold">Medicines Catalog:</span>
                    <span class="font-black text-blue-700">{{ number_format($medicinesSourceCount) }} rows</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Destination: PostgreSQL</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Connection:</span>
                    <span class="font-mono text-xs text-gray-900">{{ env('DB_CONNECTION', 'pgsql') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-bold text-green-600">Connected</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-100">
                    <span class="text-gray-600 font-semibold">Total Rows Across Tables:</span>
                    <span class="font-black text-gray-900">{{ number_format($totalDestRows) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-semibold">Medicines Catalog:</span>
                    <span class="font-black text-blue-700">{{ number_format($medicinesDestCount) }} rows</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ACTION BUTTONS --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-bold text-blue-900 mb-2">Migration Controls</h2>
        <p class="text-sm text-blue-700 mb-6">Always run a dry run first to verify schema and row counts. Real transfers are idempotent (upsert) but should be handled with care.</p>
        
        <div class="flex flex-col sm:flex-row gap-4">
            
            <form action="{{ route('admin.migration.dry_run', ['token' => request()->query('token')]) }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-white border-2 border-blue-600 text-blue-700 hover:bg-blue-600 hover:text-white rounded-lg font-bold transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Run Dry Run
                </button>
            </form>

            <form action="{{ route('admin.migration.real_transfer', ['token' => request()->query('token')]) }}" method="POST" onsubmit="return confirm('Are you sure you want to run the REAL transfer? This will upsert data into the production database.');">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-red-600 border-2 border-red-600 text-white hover:bg-red-700 hover:border-red-700 rounded-lg font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    RUN REAL TRANSFER
                </button>
            </form>

        </div>
    </div>

    {{-- OUTPUT PANELS --}}
    @if (session('dry_run_output'))
        <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden border border-gray-700 mt-6">
            <div class="px-4 py-2 bg-gray-800 border-b border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                Dry Run Output
            </div>
            <div class="p-4 overflow-x-auto">
                <pre class="text-sm font-mono text-gray-300 leading-relaxed">{{ session('dry_run_output') }}</pre>
            </div>
        </div>
    @endif

    @if (session('real_transfer_output'))
        <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden border border-gray-700 mt-6">
            <div class="px-4 py-2 bg-gray-800 border-b border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                Real Transfer Output
            </div>
            <div class="p-4 overflow-x-auto">
                <pre class="text-sm font-mono text-gray-300 leading-relaxed">{{ session('real_transfer_output') }}</pre>
            </div>
        </div>
    @endif

</div>
@endsection
