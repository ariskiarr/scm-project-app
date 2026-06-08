@extends('layouts.app')

@section('title', 'Rekap Transaksi Harian')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Rekap Transaksi Harian</h1>
            <p class="text-slate-500 text-sm mt-1">Buat rekapitulasi penjualan harian gerai untuk diserahkan ke pemilik usaha.</p>
        </div>

        <form action="{{ route('kasir.rekap.generate') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Proses Rekap Hari Ini
            </button>
        </form>
    </div>

    <!-- Today's Live Status -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 mb-8">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Rekap Hari Ini: {{ today()->format('d M Y') }}</h3>
        @if($todaySummary)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mt-4">
                <div class="bg-slate-50 p-4 rounded-2xl">
                    <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                    <span class="block text-xl font-bold text-amber-600 mt-1">Rp {{ number_format($todaySummary->net_sales, 0, ',', '.') }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl">
                    <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider">Transaksi Offline</span>
                    <span class="block text-xl font-bold text-slate-800 mt-1">{{ $todaySummary->total_orders_offline }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl">
                    <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider">Transaksi Online</span>
                    <span class="block text-xl font-bold text-slate-800 mt-1">{{ $todaySummary->total_orders_online }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl">
                    <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider">Order Dibatalkan</span>
                    <span class="block text-xl font-bold text-red-500 mt-1">{{ $todaySummary->cancelled_orders }}</span>
                </div>
            </div>
            <p class="text-2xs text-slate-400 mt-4 font-medium">Terakhir di-generate oleh: <span class="text-slate-600">{{ $todaySummary->generatedBy ? $todaySummary->generatedBy->name : 'System' }}</span> pada {{ $todaySummary->updated_at->format('H:i:s') }}</p>
        @else
            <div class="bg-amber-50 border border-amber-100 p-6 rounded-2xl mt-4 text-center">
                <p class="text-sm text-amber-800">Penjualan hari ini belum direkap secara resmi.</p>
                <p class="text-xs text-amber-600 mt-1">Gunakan tombol "Proses Rekap Hari Ini" di atas untuk menyimpan rekap transaksi.</p>
            </div>
        @endif
    </div>

    <!-- History summaries -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Riwayat Rekap Harian</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 text-3xs font-bold uppercase tracking-wider border-b border-slate-50">
                        <th class="pb-3">Tanggal Rekap</th>
                        <th class="pb-3 text-center">Total Transaksi</th>
                        <th class="pb-3 text-center">Offline</th>
                        <th class="pb-3 text-center">Online</th>
                        <th class="pb-3 text-right">Omset Kotor</th>
                        <th class="pb-3 text-right">Diskon Toko</th>
                        <th class="pb-3 text-right">Pendapatan Bersih</th>
                        <th class="pb-3 text-center">Direkap Oleh</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-600 divide-y divide-slate-50">
                    @forelse($summaries as $sum)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 font-bold text-slate-800">
                                {{ $sum->summary_date->format('d M Y') }}
                            </td>
                            <td class="py-3 text-center">
                                {{ $sum->total_transactions }}
                            </td>
                            <td class="py-3 text-center">
                                {{ $sum->total_orders_offline }}
                            </td>
                            <td class="py-3 text-center">
                                {{ $sum->total_orders_online }}
                            </td>
                            <td class="py-3 text-right text-slate-500">
                                Rp {{ number_format($sum->gross_sales, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right text-red-500">
                                -Rp {{ number_format($sum->total_discount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-bold text-amber-600">
                                Rp {{ number_format($sum->net_sales, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-center text-slate-400">
                                {{ $sum->generatedBy ? $sum->generatedBy->name : 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada riwayat rekap harian yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
