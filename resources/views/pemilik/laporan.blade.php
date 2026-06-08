@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan Penjualan</h1>
        <p class="text-slate-500 text-sm mt-1">Laporan transaksi penjualan harian outlet dan pesanan online pelanggan.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 mb-8">
        <form action="{{ route('pemilik.laporan') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
            </div>
            <div class="flex-1">
                <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
            </div>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm px-6 py-2.5 rounded-xl shadow-md transition-all duration-150">
                Filter Laporan
            </button>
        </form>
    </div>

    <!-- Summary Box Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pendapatan Bersih</span>
            <span class="block text-2xl font-bold text-amber-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
            <span class="block text-2xl font-bold text-slate-800 mt-1">{{ $totalTransactions }} Transaksi</span>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pesanan Online</span>
            <span class="block text-2xl font-bold text-blue-600 mt-1">{{ $totalOnline }} Order</span>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penjualan Kasir (Offline)</span>
            <span class="block text-2xl font-bold text-emerald-600 mt-1">{{ $totalOffline }} Order</span>
        </div>
    </div>

    <!-- Laporan Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Tanggal Rekap</th>
                        <th class="py-4 px-6 text-center">Toko (Offline)</th>
                        <th class="py-4 px-6 text-center">Online</th>
                        <th class="py-4 px-6 text-right">Omset Kotor</th>
                        <th class="py-4 px-6 text-right">Diskon</th>
                        <th class="py-4 px-6 text-right">Ongkir</th>
                        <th class="py-4 px-6 text-right">Pendapatan Bersih</th>
                        <th class="py-4 px-6 text-center">Batal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                    @forelse($summaries as $summary)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-bold text-slate-800">
                                {{ $summary->summary_date->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                {{ $summary->total_orders_offline }}
                            </td>
                            <td class="py-4 px-6 text-center text-blue-600 font-semibold">
                                {{ $summary->total_orders_online }}
                            </td>
                            <td class="py-4 px-6 text-right text-slate-500">
                                Rp {{ number_format($summary->gross_sales, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-right text-red-500">
                                -Rp {{ number_format($summary->total_discount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-right text-slate-500">
                                Rp {{ number_format($summary->total_shipping_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-right font-bold text-amber-600">
                                Rp {{ number_format($summary->net_sales, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center text-red-500 font-semibold">
                                {{ $summary->cancelled_orders }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Tidak ada rekap penjualan untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
