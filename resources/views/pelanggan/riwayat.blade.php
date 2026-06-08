@extends('layouts.app')

@section('title', 'Riwayat Pesanan Anda')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Riwayat Pesanan</h1>
        <p class="text-slate-500 text-sm mt-1">Daftar seluruh pesanan kebab yang pernah Anda lakukan sebelumnya.</p>
    </div>

    <!-- History list -->
    <div class="space-y-4">
        @forelse($orders as $ord)
            <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-3 border-b border-slate-50 text-xs">
                    <div>
                        <span class="font-bold text-slate-800">{{ $ord->order_number }}</span>
                        <span class="text-slate-400 pl-2">{{ $ord->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-3xs font-bold uppercase tracking-wider
                        @if($ord->status === 'pending') bg-red-50 text-red-700
                        @elseif($ord->status === 'delivered') bg-emerald-50 text-emerald-700
                        @elseif($ord->status === 'cancelled') bg-slate-100 text-slate-400
                        @else bg-amber-50 text-amber-700 @endif">
                        {{ $ord->status_label }}
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-3 text-xs">
                    <!-- Products summary -->
                    <div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($ord->items as $item)
                                <span class="inline-flex items-center rounded-lg bg-slate-50 border border-slate-100 px-2 py-0.5 text-2xs text-slate-600">
                                    {{ $item->product_name }} ({{ floatval($item->quantity) }} pcs)
                                </span>
                            @endforeach
                        </div>
                        <p class="text-3xs text-slate-400 mt-2">
                            Metode Pembayaran: <span class="font-bold uppercase">{{ $ord->payment_method }}</span> |
                            Status Bayar: <span class="font-bold {{ $ord->is_paid ? 'text-emerald-600' : 'text-red-500' }}">{{ $ord->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}</span>
                        </p>
                    </div>

                    <!-- Right part: Total and tracking link -->
                    <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                        <div>
                            <span class="text-3xs text-slate-400 block text-left sm:text-right">Total Belanja</span>
                            <span class="font-bold text-sm text-slate-800">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($ord->status !== 'cancelled')
                            <a href="{{ route('pelanggan.tracking', $ord->id) }}" class="border border-amber-500 text-amber-600 hover:bg-amber-50 font-bold text-2xs px-3.5 py-2 rounded-xl transition">
                                Lacak Pesanan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Anda belum pernah melakukan pesanan online.
            </div>
        @endforelse
    </div>
</div>
@endsection
