@extends('layouts.admin')
@section('title', 'WhatsApp Logs')
@section('page-title', 'WhatsApp Logs')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Riwayat WhatsApp</h2>
        
        <form action="{{ route('wa-logs.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" placeholder="Cari nomor..." 
                   value="{{ request('search') }}"
                   class="border rounded px-3 py-1 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            
            <select name="status" class="border rounded px-3 py-1 text-sm outline-none" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            
            <button type="submit" class="bg-gray-800 text-white px-4 py-1 rounded text-sm hover:bg-black">Cari</button>
            <a href="{{ route('wa-logs.index') }}" class="bg-gray-200 px-4 py-1 rounded text-sm text-gray-600">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                <tr>
                    <th class="p-4 font-semibold">Waktu</th>
                    <th class="p-4 font-semibold">Nomor Tujuan</th>
                    <th class="p-4 font-semibold">Pesan</th>
                    <th class="p-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-sm text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                    <td class="p-4 font-medium text-gray-700">{{ $log->target }}</td>
                    <td class="p-4 text-sm text-gray-600">
                        <span title="{{ $log->message }}">{{ Str::limit($log->message, 40) }}</span>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $log->status == 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ strtoupper($log->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-400">Belum ada riwayat pengiriman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection