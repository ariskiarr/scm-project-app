@extends('layouts.app')

@section('title', 'Dashboard Kurir')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openUpdateModal: false, activeOrderId: null }">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Kurir</h1>
        <p class="text-slate-500 text-xs mt-1">Pantau dan update status pengiriman kebab ke pelanggan secara langsung.</p>
    </div>

    <!-- Shippings Queue -->
    <div class="space-y-6">
        @forelse($orders as $ord)
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-base text-slate-800">{{ $ord->order_number }}</span>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-2xs font-bold uppercase tracking-wider
                                @if($ord->status === 'pending') bg-red-50 text-red-700
                                @elseif($ord->status === 'confirmed') bg-blue-50 text-blue-700
                                @elseif($ord->status === 'processing') bg-amber-50 text-amber-700
                                @elseif($ord->status === 'ready_to_ship') bg-indigo-50 text-indigo-700
                                @elseif($ord->status === 'shipped') bg-purple-50 text-purple-700
                                @elseif($ord->status === 'delivered') bg-emerald-50 text-emerald-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $ord->status_label }}
                            </span>
                        </div>
                        <p class="text-2xs text-slate-400 mt-1">
                            Pelanggan: <span class="font-semibold text-slate-600">{{ $ord->customer->name }}</span> |
                            Kontak: <span class="font-semibold text-slate-600">{{ $ord->customer->phone }}</span>
                        </p>
                    </div>
                    <button @click="activeOrderId = {{ $ord->id }}; openUpdateModal = true" class="bg-amber-500 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-md shadow-amber-500/10 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Update Lokasi & Status
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 text-xs">
                    <!-- Delivery Address & Notes -->
                    <div class="md:col-span-2 space-y-2">
                        <div>
                            <span class="block text-2xs font-bold text-slate-400 uppercase tracking-wider">Alamat Kirim:</span>
                            <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $ord->shipping_address ?: '-' }}</p>
                        </div>
                        @if($ord->notes)
                            <div class="pt-2">
                                <span class="block text-2xs font-bold text-slate-400 uppercase tracking-wider">Catatan Pengiriman:</span>
                                <p class="text-slate-500 italic mt-0.5">"{{ $ord->notes }}"</p>
                            </div>
                        @endif
                    </div>

                    <!-- Payment and Total -->
                    <div class="bg-slate-50/50 p-4 border border-slate-100 rounded-2xl flex flex-col justify-between">
                        <div>
                            <span class="block text-2xs font-bold text-slate-400 uppercase tracking-wider mb-1">Metode Pembayaran:</span>
                            <span class="font-bold text-slate-700 block uppercase">{{ $ord->payment_method }}</span>
                            <span class="inline-block mt-1 font-bold text-2xs {{ $ord->is_paid ? 'text-emerald-600' : 'text-red-500' }}">
                                @if($ord->is_paid)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> LUNAS
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.34c-.22.38-.22.86 0 1.24.22.38.62.62 1.04.62h16.94c.42 0 .82-.24 1.04-.62.22-.38.22-.86 0-1.24l-8.3-14.34c-.22-.38-.62-.62-1.04-.62s-.82.24-1.04.62z"/></svg> HARUS BAYAR DI TEMPAT (COD)
                                @endif
                            </span>
                        </div>

                        <div class="border-t border-slate-100 pt-3 mt-3 flex justify-between items-center">
                            <span class="font-semibold text-slate-500">Nilai Tagihan:</span>
                            <span class="font-bold text-sm text-amber-600">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Tidak ada pesanan kebab yang ditugaskan ke Anda saat ini.
            </div>
        @endforelse
    </div>

    <!-- Modal Update Delivery Status -->
    <div x-show="openUpdateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openUpdateModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Update Status Kirim</h3>
                <button @click="openUpdateModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form :action="`{{ url('kurir/pengiriman') }}/${activeOrderId}/update`" method="POST" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Status Kirim</label>
                    <select name="status" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-amber-500 focus:outline-none">
                        <option value="picked_up">Paket Diambil Dari Dapur (Picked Up)</option>
                        <option value="in_transit">Sedang Di Jalan (In Transit)</option>
                        <option value="delivered">Paket Sampai & Diterima Pelanggan (Delivered)</option>
                        <option value="failed_delivery">Gagal Kirim (Rumah Kosong / Salah Alamat)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Lokasi Terkini</label>
                    <input type="text" name="location" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-amber-500 focus:outline-none" placeholder="Contoh: Jl. Sudirman / Dekat Gerbang Perumahan">
                </div>

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Keterangan Tambahan</label>
                    <textarea name="description" required rows="2" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-amber-500 focus:outline-none" placeholder="Contoh: Paket diterima langsung oleh pelanggan..."></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openUpdateModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-xs font-semibold text-white shadow-md hover:bg-amber-600 transition">Simpan Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
