@extends('layouts.admin')
@section('title', 'WhatsApp Logs')
@section('page-title', 'WhatsApp Logs')

@section('content')

{{-- Filter bar --}}
<div class="bg-white rounded-2xl border border-gray-100 px-4 py-3.5 mb-4" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">
    <form action="{{ route('wa-logs.index') }}" method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="flex-1 min-w-[160px] relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nomor tujuan..."
                class="w-full bg-gray-50 border border-gray-200 text-[12px] rounded-lg pl-8 pr-3 py-2 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/10 transition font-[inherit]">
        </div>

        <select name="status" class="bg-gray-50 border border-gray-200 text-[12px] rounded-lg px-3 py-2 outline-none focus:border-emerald-400 transition font-[inherit]" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
            <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>Failed</option>
        </select>

        <button type="submit"
            class="bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5">
            <i class="fa-solid fa-filter text-[10px]"></i> Filter
        </button>

        @if(request('search') || request('status'))
        <a href="{{ route('wa-logs.index') }}"
            class="bg-gray-100 hover:bg-gray-200 text-gray-500 text-[12px] font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5">
            <i class="fa-solid fa-xmark text-[10px]"></i> Reset
        </a>
        @endif
    </form>
</div>

{{-- Table card --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,0.04);">

    {{-- Header --}}
    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-800 text-[13px]">Riwayat Pengiriman WhatsApp</h3>
            <p class="text-[10.5px] text-gray-400 mt-0.5">Log notifikasi otomatis via WhatsApp</p>
        </div>
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.08);">
            <i class="fa-brands fa-whatsapp text-emerald-500 text-[15px]"></i>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[560px]">
            <thead>
                <tr class="bg-gray-50/70">
                    <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Waktu</th>
                    <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nomor Tujuan</th>
                    <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pesan</th>
                    <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50/60 transition-colors group">

                    {{-- Waktu --}}
                    <td class="px-5 py-3.5">
                        <div class="text-[12px] font-semibold text-gray-700 leading-tight">
                            {{ $log->created_at->format('d M Y') }}
                        </div>
                        <div class="text-[10px] text-gray-400 mt-0.5">
                            {{ $log->created_at->format('H:i') }}
                            <span class="ml-1 text-gray-300">·</span>
                            <span class="ml-1">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </td>

                    {{-- Nomor --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.08);">
                                <i class="fa-brands fa-whatsapp text-emerald-500 text-[11px]"></i>
                            </div>
                            <span class="font-mono text-[12.5px] font-semibold text-gray-800">{{ $log->target }}</span>
                        </div>
                    </td>

                    {{-- Pesan --}}
                    <td class="px-5 py-3.5">
                        <span class="text-[12px] text-gray-600 leading-relaxed" title="{{ $log->message }}">
                            {{ Str::limit($log->message, 55) }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-3.5 text-center">
                        @if($log->status === 'success')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9.5px] font-bold uppercase tracking-wide"
                                  style="background:rgba(16,185,129,0.1); color:#059669;">
                                <i class="fa-solid fa-check text-[8px]"></i> Terkirim
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9.5px] font-bold uppercase tracking-wide"
                                  style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca;">
                                <i class="fa-solid fa-xmark text-[8px]"></i> Gagal
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-16 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-brands fa-whatsapp text-xl text-gray-300"></i>
                        </div>
                        <p class="text-[12px] text-gray-400 font-medium">Belum ada riwayat pengiriman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between flex-wrap gap-2">
        <p class="text-[11px] text-gray-400">
            Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log
        </p>
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection