@extends('layouts.app')

@section('title', 'Dashboard Pemilik')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Utama</h1>
        <p class="text-slate-500 text-sm mt-1">Ringkasan operasional supply chain dan penjualan UMKM Kebab.</p>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Revenue Card -->
        <div class="bg-gradient-to-tr from-amber-500 to-amber-600 rounded-3xl p-6 text-white shadow-xl shadow-amber-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-7xl opacity-10 select-none transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <span class="block text-xs uppercase tracking-wider font-semibold opacity-75">Penjualan Harian</span>
            <span class="block text-2xl font-bold mt-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
            <span class="block text-xs opacity-75 mt-2">{{ $todayOrdersCount }} Transaksi Sukses Hari Ini</span>
        </div>

        <!-- Stock Warnings Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-7xl opacity-10 select-none transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
            </div>
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-400">Pemberitahuan Stok</span>
            @if($lowStockMaterials->count() > 0)
                <span class="block text-2xl font-bold text-red-500 mt-2">{{ $lowStockMaterials->count() }} Bahan</span>
                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-2xs font-bold text-red-700 mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.34c-.22.38-.22.86 0 1.24.22.38.62.62 1.04.62h16.94c.42 0 .82-.24 1.04-.62.22-.38.22-.86 0-1.24l-8.3-14.34c-.22-.38-.62-.62-1.04-.62s-.82.24-1.04.62z" /></svg>
                    Perlu Restock Segera
                </span>
            @else
                <span class="block text-2xl font-bold text-emerald-600 mt-2">Stok Aman</span>
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-2xs font-bold text-emerald-700 mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Semua Di Atas Minimum
                </span>
            @endif
        </div>

        <!-- Active POs Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-7xl opacity-10 select-none transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-400">PO Bahan Baku Aktif</span>
            <span class="block text-2xl font-bold text-slate-800 mt-2">{{ $activePOs->count() }} Pesanan</span>
            <span class="block text-xs text-slate-400 mt-3">Sedang Diproses/Kirim Pemasok</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Low Stock Alerts Section -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Notifikasi Stok Menipis</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Bahan baku yang sudah mendekati atau di bawah batas minimum.</p>
                </div>
                <a href="{{ route('pemilik.bahan-baku') }}" class="text-xs text-amber-600 font-bold hover:text-amber-700">Manajemen Stok &rarr;</a>
            </div>

            <div class="space-y-4">
                @forelse($lowStockMaterials as $mat)
                    <div class="flex items-center justify-between p-4 bg-red-50/50 border border-red-100 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.34c-.22.38-.22.86 0 1.24.22.38.62.62 1.04.62h16.94c.42 0 .82-.24 1.04-.62.22-.38.22-.86 0-1.24l-8.3-14.34c-.22-.38-.62-.62-1.04-.62s-.82.24-1.04.62z" /></svg>
                            <div>
                                <span class="font-bold text-sm text-slate-800 block">{{ $mat->name }}</span>
                                <span class="text-2xs text-slate-500 block">Kode: {{ $mat->code }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400 block">Stok / Batas Min</span>
                            <span class="font-bold text-sm text-red-600">{{ floatval($mat->current_stock) }} / {{ floatval($mat->minimum_stock) }} {{ $mat->unit }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-semibold text-slate-600 mt-3">Semua stok bahan baku aman!</p>
                        <p class="text-xs text-slate-400 mt-1">Stok saat ini berada di atas batas minimum pemesanan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- PO Monitoring Section -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Status Pengiriman PO</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pantau status pesanan bahan baku dari pemasok.</p>
                </div>
                <a href="{{ route('pemilik.purchase-orders') }}" class="text-xs text-amber-600 font-bold hover:text-amber-700">Semua PO &rarr;</a>
            </div>

            <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                @forelse($activePOs as $po)
                    <div class="p-4 border border-slate-100 hover:border-slate-200 rounded-2xl transition">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-bold text-sm text-slate-800 block">{{ $po->po_number }}</span>
                                <span class="text-2xs text-slate-400 block">Pemasok: {{ $po->supplier->company_name }}</span>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-2xs font-bold uppercase tracking-wider
                                @if($po->status === 'draft') bg-slate-100 text-slate-700
                                @elseif($po->status === 'sent') bg-blue-100 text-blue-700
                                @elseif($po->status === 'confirmed') bg-indigo-100 text-indigo-700
                                @elseif($po->status === 'shipped') bg-amber-100 text-amber-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ $po->status }}
                            </span>
                        </div>
                        <div class="mt-3 bg-slate-50 p-3 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="text-3xs text-slate-400 uppercase tracking-wider font-semibold block">Update Terbaru</span>
                                <p class="text-xs text-slate-600 mt-0.5 font-medium">
                                    {{ $po->latestDeliveryUpdate ? $po->latestDeliveryUpdate->description : 'Belum ada update.' }}
                                </p>
                            </div>
                            @if($po->status === 'shipped')
                                <form action="{{ route('pemilik.purchase-orders.receive', $po->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-2xs px-3 py-1.5 rounded-lg shadow-sm shadow-emerald-500/10 transition">
                                        Terima Bahan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-1m0 0l2 1m-2-1v2a1 1 0 001 1h14a1 1 0 001-1v-2m0 0l2 1m-2-1l-2 1" /></svg>
                        <p class="text-sm font-semibold text-slate-600 mt-3">Tidak ada pengiriman bahan baku aktif.</p>
                        <p class="text-xs text-slate-400 mt-1">Buat PO baru untuk melakukan pemesanan bahan baku.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
