@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('pelanggan.dashboard') }}" class="text-xs text-amber-600 font-bold hover:text-amber-700 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Menu
        </a>
    </div>

    <!-- Order Header Card -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $order->order_number }}</h1>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-2xs font-bold uppercase tracking-wider mt-1
                    @if($order->status === 'pending') bg-red-50 text-red-700
                    @elseif($order->status === 'confirmed') bg-blue-50 text-blue-700
                    @elseif($order->status === 'processing') bg-amber-50 text-amber-700
                    @elseif($order->status === 'ready_to_ship') bg-indigo-50 text-indigo-700
                    @elseif($order->status === 'shipped') bg-purple-50 text-purple-700
                    @elseif($order->status === 'delivered') bg-emerald-50 text-emerald-700
                    @else bg-slate-100 text-slate-700 @endif">
                    {{ $order->status_label }}
                </span>
            </div>
            <div>
                <span class="block text-2xs font-bold text-slate-400 uppercase tracking-wider">Metode Pembayaran</span>
                <span class="font-bold text-slate-800 text-sm block uppercase">{{ $order->payment_method }}</span>
                <span class="inline-block mt-1 font-bold text-2xs {{ $order->is_paid ? 'text-emerald-600' : 'text-red-500' }}">
                    @if($order->payment_status === 'paid')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> LUNAS
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"/></svg> BELUM LUNAS
                    @endif
                </span>
            </div>
            <div class="text-left sm:text-right">
                <span class="block text-2xs font-bold text-slate-400 uppercase tracking-wider">Total Pesanan</span>
                <span class="text-lg font-bold text-amber-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Courier Details if assigned -->
        @if($order->kurir)
            <div class="mt-4 bg-slate-50 p-4 rounded-2xl flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <div>
                    <span class="block text-2xs font-bold text-slate-400 uppercase tracking-wider">Kurir Pengirim</span>
                    <span class="font-bold text-slate-800 text-sm block">{{ $order->kurir->name }}</span>
                    <span class="text-xs text-slate-500">No HP/WA: <span class="font-semibold text-slate-600">{{ $order->kurir->phone }}</span></span>
                </div>
            </div>
        @endif
    </div>

    <!-- Timeline Delivery Updates -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Riwayat Pergerakan Pengiriman</h3>

        @forelse($deliveryUpdates as $update)
            <div class="flex pb-6 relative">
                <!-- Timeline Dot & Connector -->
                <div class="flex flex-col items-center mr-3">
                    <div class="w-2.5 h-2.5 rounded-full border-2
                        @if($loop->first) bg-amber-500 border-amber-500
                        @else bg-white border-slate-300 @endif">
                    </div>
                    @if(!$loop->last)
                        <div class="w-0.5 flex-1 bg-slate-200 mt-1.5"></div>
                    @endif
                </div>

                <div class="flex-1 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-2xs font-bold uppercase tracking-wider
                                @if($update->status === 'processing') bg-amber-100 text-amber-800
                                @elseif($update->status === 'picked_up') bg-indigo-100 text-indigo-800
                                @elseif($update->status === 'in_transit') bg-blue-100 text-blue-800
                                @elseif($update->status === 'delivered') bg-emerald-100 text-emerald-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $update->status_label }}
                            </span>
                            <span class="font-bold text-sm text-slate-800 block mt-1">{{ $update->description }}</span>
                            @if($update->location)
                                <p class="text-2xs text-slate-400 mt-1 flex items-center gap-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lokasi: {{ $update->location }}
                                </p>
                            @endif
                        </div>
                        <span class="text-2xs text-slate-400 shrink-0 mt-0.5 font-semibold">{{ $update->created_at->format('d M H:i') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Belum ada update pengiriman.
            </div>
        @endforelse
    </div>

    <!-- Shipping Info -->
    <div class="mt-6 bg-amber-50 border border-amber-100 rounded-3xl p-5">
        <div class="flex items-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-xs font-bold text-amber-800">Butuh bantuan?</p>
                <p class="text-2xs text-amber-700 mt-0.5">Hubungi tim dukung kami melalui WhatsApp apabila ada kendala pengiriman.</p>
            </div>
        </div>
    </div>
</div>
@endsection
