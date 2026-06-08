@extends('layouts.app')

@section('title', 'Dashboard Pemasok')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openShipmentModal: false, activePOId: null }">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen PO Masuk</h1>
        <p class="text-slate-500 text-sm mt-1">Periksa pesanan bahan baku dari pemilik gerai, lakukan konfirmasi, dan input status pengiriman.</p>
    </div>

    <!-- PO Queue -->
    <div class="space-y-6">
        @forelse($purchaseOrders as $po)
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg text-slate-800">{{ $po->po_number }}</span>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-3xs font-bold uppercase tracking-wider
                                @if($po->status === 'sent') bg-blue-50 text-blue-700
                                @elseif($po->status === 'confirmed') bg-indigo-50 text-indigo-700
                                @elseif($po->status === 'shipped') bg-amber-50 text-amber-700
                                @elseif($po->status === 'received') bg-emerald-50 text-emerald-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $po->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Pemesan: <span class="font-semibold text-slate-600">{{ $po->creator->name }}</span> |
                            Estimasi Tiba: <span class="font-semibold text-slate-600">{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('d M Y') : '-' }}</span> |
                            Tanggal Order: <span class="font-semibold text-slate-600">{{ $po->created_at->format('d M Y H:i') }}</span>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        @if($po->status === 'sent')
                            <form action="{{ route('pemasok.purchase-orders.confirm', $po->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                    Konfirmasi & Siapkan Barang
                                </button>
                            </form>
                        @elseif($po->status === 'confirmed' || $po->status === 'preparing')
                            <button @click="activePOId = {{ $po->id }}; openShipmentModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                Update Status Pengiriman
                            </button>
                        @elseif($po->status === 'shipped')
                            <button @click="activePOId = {{ $po->id }}; openShipmentModal = true" class="border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-semibold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                Tambah Update Log Transit
                            </button>
                        @endif
                    </div>
                </div>

                <!-- PO Items Detail & Address -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 text-xs">
                    <!-- Products Ordered -->
                    <div class="md:col-span-2">
                        <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider mb-2">Item Bahan Baku yang Dipesan:</span>
                        <div class="space-y-2">
                            @foreach($po->items as $item)
                                <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl">
                                    <div>
                                        <span class="font-semibold text-slate-800">{{ $item->rawMaterial->name }}</span>
                                        <span class="text-slate-400 block text-3xs">{{ floatval($item->quantity) }} {{ $item->unit }} x Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="font-bold text-slate-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- PO summary card -->
                    <div class="bg-slate-50/50 p-4 border border-slate-100 rounded-2xl flex flex-col justify-between">
                        <div>
                            @if($po->notes)
                                <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider mb-1">Catatan Pemilik:</span>
                                <p class="text-slate-500 italic leading-normal">"{{ $po->notes }}"</p>
                            @endif

                            <span class="block text-3xs font-bold text-slate-400 uppercase tracking-wider mt-3 mb-1">Update Status Log Terkini:</span>
                            <p class="text-slate-700 leading-normal font-semibold">
                                {{ $po->latestDeliveryUpdate ? $po->latestDeliveryUpdate->status_label : 'Belum ada update log.' }}
                            </p>
                            <p class="text-slate-500 text-3xs leading-normal mt-0.5">
                                {{ $po->latestDeliveryUpdate ? $po->latestDeliveryUpdate->description : '' }}
                            </p>
                            @if($po->latestDeliveryUpdate && $po->latestDeliveryUpdate->tracking_number)
                                <p class="text-3xs text-purple-600 font-bold uppercase tracking-wider mt-1.5">No Resi: {{ $po->latestDeliveryUpdate->tracking_number }}</p>
                            @endif
                        </div>

                        <div class="border-t border-slate-100 pt-3 mt-3 flex justify-between items-center">
                            <span class="font-semibold text-slate-500">Nilai Tagihan PO:</span>
                            <span class="font-bold text-sm text-amber-600">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Tidak ada Purchase Order dari pemilik gerai saat ini.
            </div>
        @endforelse
    </div>

    <!-- Modal Update Shipment -->
    <div x-show="openShipmentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openShipmentModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Update Pengiriman PO</h3>
                <button @click="openShipmentModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="`{{ url('pemasok/purchase-orders') }}/${activePOId}/pengiriman`" method="POST" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Status Log Pengiriman</label>
                    <select name="status" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                        <option value="preparing">Sedang Disiapkan / Packing</option>
                        <option value="shipped">Sudah Diserahkan ke Ekspedisi (Shipped)</option>
                        <option value="in_transit">Sedang Dalam Perjalanan Transit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Nomor Resi / Kurir (Opsional)</label>
                    <input type="text" name="tracking_number" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="Contoh: JNE-9842014022">
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Keterangan / Lokasi Terkini</label>
                    <textarea name="description" required rows="2" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="Contoh: Paket telah dikirim lewat kurir internal..."></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openShipmentModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition">Kirim Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
