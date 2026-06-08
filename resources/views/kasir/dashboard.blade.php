@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Kasir</h1>
            <p class="text-slate-500 text-sm mt-1">Pusat pencatatan transaksi gerai offline dan antrean pesanan online.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('kasir.transaksi.create') }}" class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
                Kasir Baru (POS)
            </a>
            <a href="{{ route('kasir.pesanan') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                Antrean Pesanan
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-400">Total Transaksi Kasir Hari Ini</span>
            <span class="block text-3xl font-bold text-slate-800 mt-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-400">Menunggu Konfirmasi</span>
            <span class="block text-3xl font-bold text-amber-500 mt-2">{{ $pendingCount }} Pesanan</span>
        </div>
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-400">Total Pesanan Diproses</span>
            <span class="block text-3xl font-bold text-indigo-600 mt-2">
                {{ $todayOrders->whereIn('status', ['confirmed', 'processing', 'ready_to_ship'])->count() }} Pesanan
            </span>
        </div>
    </div>

    <!-- Active Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Antrean Pesanan Aktif Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 text-3xs font-bold uppercase tracking-wider border-b border-slate-50">
                        <th class="pb-3">No Pesanan</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Metode Bayar</th>
                        <th class="pb-3">Total Belanja</th>
                        <th class="pb-3">Kurir Pengirim</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-600 divide-y divide-slate-50">
                    @forelse($activeOrders as $ord)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3">
                                <span class="font-bold text-slate-800 block">{{ $ord->order_number }}</span>
                                <span class="text-3xs text-slate-400 block">{{ $ord->created_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="py-3">
                                <span class="font-semibold text-slate-700 block">{{ $ord->customer->name }}</span>
                                <span class="text-3xs text-slate-400 block">{{ $ord->customer->phone }}</span>
                            </td>
                            <td class="py-3">
                                <span class="uppercase font-bold text-slate-500 block">{{ $ord->payment_method }}</span>
                                <span class="text-3xs {{ $ord->is_paid ? 'text-emerald-600' : 'text-red-500' }} block font-bold">
                                    {{ $ord->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                                </span>
                            </td>
                            <td class="py-3 font-bold text-slate-800">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-slate-400">
                                {{ $ord->kurir ? $ord->kurir->name : 'Belum Ditunjuk' }}
                            </td>
                            <td class="py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-3xs font-bold uppercase tracking-wider
                                    @if($ord->status === 'pending') bg-red-50 text-red-700
                                    @elseif($ord->status === 'confirmed') bg-blue-50 text-blue-700
                                    @elseif($ord->status === 'processing') bg-amber-50 text-amber-700
                                    @else bg-emerald-50 text-emerald-700 @endif">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                <a href="{{ route('kasir.pesanan') }}" class="text-amber-600 hover:text-amber-700 font-bold">
                                    Kelola &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada antrean pesanan aktif saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
