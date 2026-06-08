@extends('layouts.app')

@section('title', 'Konfirmasi Checkout')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('pelanggan.dashboard') }}" class="text-xs text-amber-600 font-bold hover:text-amber-700">&larr; Kembali Belanja</a>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-2">Konfirmasi Pesanan Anda</h1>
        <p class="text-slate-500 text-sm mt-1">Lengkapi informasi pengiriman dan pilih metode pembayaran untuk menyelesaikan pesanan.</p>
    </div>

    <form action="{{ route('pelanggan.order') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        <!-- Hidden inputs of items for POST submission -->
        @foreach($checkoutItems as $index => $item)
            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product']->id }}">
            <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
        @endforeach

        <!-- Left Column: Items and delivery info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Items Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Rincian Menu</h3>
                <div class="space-y-3">
                    @foreach($checkoutItems as $item)
                        <div class="flex justify-between items-center bg-slate-50 p-3.5 rounded-2xl">
                            <div>
                                <span class="font-bold text-sm text-slate-800 block">{{ $item['product']->name }}</span>
                                <span class="text-slate-400 text-xs font-medium">{{ floatval($item['quantity']) }} pcs x Rp {{ number_format($item['product']->price, 0, ',', '.') }}</span>
                            </div>
                            <span class="font-bold text-slate-800">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Delivery Details Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Detail Pengiriman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap Pengiriman</label>
                        <textarea name="shipping_address" required rows="3"
                                  class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none"
                                  placeholder="Masukkan alamat pengiriman lengkap...">{{ auth()->user()->address }}</textarea>
                    </div>

                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Catatan Tambahan untuk Kasir/Dapur (Opsional)</label>
                        <textarea name="notes" rows="2"
                                  class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none"
                                  placeholder="Contoh: Kebab extra pedas, atau jangan pakai mayones..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Payment & checkout triggers -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 sticky top-24">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Rincian Pembayaran</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Pilih Cara Pembayaran</label>
                        <select name="payment_method" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                            <option value="cod">Bayar di Tempat (COD)</option>
                            <option value="qris">QRIS (Otomatis)</option>
                            <option value="transfer">Bank Transfer Mandiri</option>
                        </select>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl space-y-2 text-xs border border-slate-100">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal Menu</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Biaya Pengiriman</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-slate-800 border-t border-slate-100 pt-2">
                            <span>Total Pembayaran</span>
                            <span class="text-amber-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full flex justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/10 hover:bg-amber-600 focus:outline-none transition-all duration-150">
                            Pesan Sekarang &rarr;
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
