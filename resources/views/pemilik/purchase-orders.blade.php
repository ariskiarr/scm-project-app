@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openCreateSelectModal: false }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Purchase Orders</h1>
            <p class="text-slate-500 text-sm mt-1">Buat pesanan bahan baku ke pemasok langsung dan pantau status barang.</p>
        </div>
        <button @click="openCreateSelectModal = true" class="rounded-xl  bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-500/10 hover:from-amber-600 hover:to-red-600 transition flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            Buat PO Baru
        </button>
    </div>

    <!-- PO List Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">No PO / Tanggal</th>
                        <th class="py-4 px-6">Pemasok</th>
                        <th class="py-4 px-6">Total Belanja</th>
                        <th class="py-4 px-6 text-center">Status PO</th>
                        <th class="py-4 px-6">Estimasi Tiba</th>
                        <th class="py-4 px-6">Update Pengiriman</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                    @forelse($purchaseOrders as $po)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 block">{{ $po->po_number }}</span>
                                <span class="text-2xs text-slate-400 block">{{ $po->created_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-700 block">{{ $po->supplier->company_name }}</span>
                                <span class="text-xs text-slate-400 block">PJ: {{ $po->supplier->contact_person }}</span>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-800">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-2xs font-bold uppercase tracking-wider
                                    @if($po->status === 'draft') bg-slate-100 text-slate-700
                                    @elseif($po->status === 'sent') bg-blue-100 text-blue-700
                                    @elseif($po->status === 'confirmed') bg-indigo-100 text-indigo-700
                                    @elseif($po->status === 'shipped') bg-amber-100 text-amber-700
                                    @elseif($po->status === 'received') bg-emerald-100 text-emerald-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $po->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-700 block">
                                    {{ $po->expected_delivery_date ? $po->expected_delivery_date->format('d M Y') : '-' }}
                                </span>
                                @if($po->actual_delivery_date)
                                    <span class="text-3xs text-emerald-600 block">Diterima: {{ $po->actual_delivery_date->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500 max-w-xs">
                                <span class="font-semibold text-slate-700 block">
                                    {{ $po->latestDeliveryUpdate ? $po->latestDeliveryUpdate->status_label : '-' }}
                                </span>
                                <p class="text-slate-400 text-3xs leading-tight mt-0.5 truncate">
                                    {{ $po->latestDeliveryUpdate ? $po->latestDeliveryUpdate->description : '' }}
                                </p>
                            </td>
                            <td class="py-4 px-6 text-center space-y-1">
                                @if($po->status === 'shipped')
                                    <form action="{{ route('pemilik.purchase-orders.receive', $po->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-2xs px-2.5 py-1.5 rounded-lg transition shadow-sm">
                                            Terima Barang
                                        </button>
                                    </form>
                                @endif

                                @if($po->is_cancellable)
                                    <form action="{{ route('pemilik.purchase-orders.cancel', $po->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan Purchase Order ini?')">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:text-red-700 hover:bg-red-50 font-semibold text-xs px-2.5 py-1.5 rounded-lg transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif

                                <div class="text-2xs text-slate-400 pt-1">
                                    @foreach($po->items as $item)
                                        <div class="block truncate max-w-xs">{{ $item->rawMaterial->name }} ({{ floatval($item->quantity) }} {{ $item->unit }})</div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                Tidak ada data Purchase Order.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Pilih Pemasok -->
    <div x-show="openCreateSelectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openCreateSelectModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Buat PO Baru - Pilih Pemasok</h3>
                <button @click="openCreateSelectModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form action="{{ route('pemilik.purchase-orders.create') }}" method="GET" class="p-6 space-y-4">
                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Silakan Pilih Pemasok</label>
                    <select name="supplier_id" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->company_name }} ({{ $sup->contact_person }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openCreateSelectModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-red-500 text-sm font-semibold text-white shadow-md hover:from-amber-600 hover:to-red-600 transition">Lanjutkan &rarr;</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
