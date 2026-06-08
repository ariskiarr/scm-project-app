@extends('layouts.app')

@section('title', 'Kelola Pesanan Pelanggan')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openCourierModal: false, activeOrderId: null }">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Pesanan Pelanggan (Online)</h1>
        <p class="text-slate-500 text-sm mt-1">Konfirmasi pesanan masuk, pantau status pembayaran, dan tugaskan kurir pengiriman.</p>
    </div>

    <!-- Active Orders Queue -->
    <div class="space-y-6">
        @forelse($orders as $ord)
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg text-slate-800">{{ $ord->order_number }}</span>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-3xs font-bold uppercase tracking-wider
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
                        <p class="text-xs text-slate-400 mt-1">
                            Pelanggan: <span class="font-semibold text-slate-600">{{ $ord->customer->name }}</span> |
                            Kontak: <span class="font-semibold text-slate-600">{{ $ord->customer->phone }}</span> |
                            Waktu Order: <span class="font-semibold text-slate-600">{{ $ord->created_at->format('d M Y H:i') }}</span>
                        </p>
                    </div>

                    <!-- Actions based on status -->
                    <div class="flex gap-2">
                        @if($ord->status === 'pending')
                            <form action="{{ route('kasir.pesanan.update-status', $ord->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                    Konfirmasi & Proses Kebab
                                </button>
                            </form>
                            <form action="{{ route('kasir.pesanan.update-status', $ord->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan pesanan ini?')">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs px-3 py-2">
                                    Tolak Pesanan
                                </button>
                            </form>
                        @elseif($ord->status === 'processing')
                            <button @click="activeOrderId = {{ $ord->id }}; openCourierModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                Tandai Siap Kirim & Pilih Kurir
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Order Details & Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 text-xs">
                    <!-- Products Ordered -->
                    <div class="md:col-span-2">
                        <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider mb-2">Item Pesanan:</span>
                        <div class="space-y-2">
                            @foreach($ord->items as $item)
                                <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl">
                                    <div>
                                        <span class="font-semibold text-slate-800">{{ $item->product_name }}</span>
                                        <span class="text-slate-400 block text-3xs">{{ floatval($item->quantity) }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="font-bold text-slate-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Payment and Delivery Address -->
                    <div class="bg-slate-50/50 p-4 border border-slate-100 rounded-2xl flex flex-col justify-between">
                        <div>
                            <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider mb-1">Metode Pembayaran:</span>
                            <span class="font-bold text-slate-700 block uppercase">{{ $ord->payment_method }}</span>
                            <span class="inline-flex items-center gap-1 mt-1 font-bold text-3xs {{ $ord->is_paid ? 'text-emerald-600' : 'text-red-500' }}">
                                @if($ord->is_paid)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    LUNAS ({{ $ord->paid_at->format('H:i') }})
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.34c-.22.38-.22.86 0 1.24.22.38.62.62 1.04.62h16.94c.42 0 .82-.24 1.04-.62.22-.38.22-.86 0-1.24l-8.3-14.34c-.22-.38-.62-.62-1.04-.62s-.82.24-1.04.62z" /></svg>
                                    BELUM DIBAYAR
                                @endif
                            </span>

                            @if($ord->shipping_address)
                                <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider mt-3 mb-1">Alamat Pengiriman:</span>
                                <p class="text-slate-600 leading-normal">{{ $ord->shipping_address }}</p>
                            @endif

                            @if($ord->notes)
                                <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider mt-3 mb-1">Catatan Pelanggan:</span>
                                <p class="text-slate-500 italic">"{{ $ord->notes }}"</p>
                            @endif
                        </div>

                        <div class="border-t border-slate-100 pt-3 mt-3 flex justify-between items-center">
                            <span class="font-semibold text-slate-500">Total Tagihan:</span>
                            <span class="font-bold text-sm text-amber-600">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                Tidak ada data pesanan online aktif.
            </div>
        @endforelse
    </div>

    <!-- Modal Select Courier -->
    <div x-show="openCourierModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openCourierModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Siap Kirim - Tugaskan Kurir</h3>
                <button @click="openCourierModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="`{{ url('kasir/pesanan') }}/${activeOrderId}/update-status`" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="status" value="ready_to_ship">

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Pilih Kurir Pengirim</label>
                    <select name="kurir_id" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                        <option value="">-- Pilih Kurir --</option>
                        @foreach($couriers as $cour)
                            <option value="{{ $cour->id }}">{{ $cour->name }} ({{ $cour->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openCourierModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition">Kirim Rincian & Siap Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
