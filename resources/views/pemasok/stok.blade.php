@extends('layouts.app')

@section('title', 'Atur Ketersediaan Stok Pemasok')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openEditModal: false, activeMaterial: {} }">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Atur Ketersediaan Stok & Harga</h1>
        <p class="text-slate-500 text-sm mt-1">Perbarui stok gudang Anda, harga kontrak penawaran bahan baku, dan waktu kirim (lead time).</p>
    </div>

    <!-- Supplied Materials Catalog -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 text-3xs font-bold uppercase tracking-wider border-b border-slate-50">
                        <th class="pb-3">Nama Bahan Baku</th>
                        <th class="pb-3 text-right">Harga Kontrak Anda</th>
                        <th class="pb-3 text-center">Min. Pemesanan</th>
                        <th class="pb-3 text-center">Stok Tersedia Anda</th>
                        <th class="pb-3 text-center">Lead Time</th>
                        <th class="pb-3 text-center">Status Hubungan</th>
                        <th class="pb-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-600 divide-y divide-slate-50">
                    @forelse($materials as $mat)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3">
                                <span class="font-bold text-slate-800 block">{{ $mat->name }}</span>
                                <span class="text-3xs text-slate-400 font-mono block">Kode: {{ $mat->code }}</span>
                            </td>
                            <td class="py-3 text-right font-semibold text-slate-800">
                                Rp {{ number_format($mat->pivot->price_per_unit, 0, ',', '.') }} / {{ $mat->unit }}
                            </td>
                            <td class="py-3 text-center font-medium">
                                {{ floatval($mat->pivot->minimum_order_qty) }} {{ $mat->unit }}
                            </td>
                            <td class="py-3 text-center font-bold text-slate-700">
                                {{ floatval($mat->pivot->available_stock) }} {{ $mat->unit }}
                            </td>
                            <td class="py-3 text-center text-slate-400">
                                {{ $mat->pivot->lead_time_days }} hari
                            </td>
                            <td class="py-3 text-center">
                                @if($mat->pivot->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-3xs font-bold text-emerald-700">Aktif Pasok</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-3xs font-bold text-red-700">Tutup Pasokan</span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                <button @click="activeMaterial = {
                                    id: {{ $mat->id }},
                                    name: '{{ $mat->name }}',
                                    unit: '{{ $mat->unit }}',
                                    price_per_unit: {{ $mat->pivot->price_per_unit }},
                                    minimum_order_qty: {{ $mat->pivot->minimum_order_qty }},
                                    available_stock: {{ $mat->pivot->available_stock }},
                                    lead_time_days: {{ $mat->pivot->lead_time_days }},
                                    is_active: {{ $mat->pivot->is_active ? 1 : 0 }}
                                }; openEditModal = true"
                                class="text-amber-600 hover:text-amber-700 font-bold">
                                    Edit Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada bahan baku dikaitkan oleh pemilik untuk Anda pasok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Edit Stok -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openEditModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-sm">Update Penawaran Bahan Baku</h3>
                <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="`{{ url('pemasok/stok') }}/${activeMaterial.id}`" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <span class="block text-2xs font-bold text-slate-400 uppercase tracking-wider">Nama Bahan Baku</span>
                    <span class="block font-bold text-sm text-slate-800 mt-1" x-text="activeMaterial.name"></span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Harga Jual Anda (Rp)</label>
                        <input type="number" name="price_per_unit" :value="activeMaterial.price_per_unit" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Min. Order Qty</label>
                        <input type="number" step="0.01" name="minimum_order_qty" :value="activeMaterial.minimum_order_qty" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Stok Tersedia Anda</label>
                        <input type="number" step="0.01" name="available_stock" :value="activeMaterial.available_stock" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Lead Time (Hari)</label>
                        <input type="number" name="lead_time_days" :value="activeMaterial.lead_time_days" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Status Pasokan</label>
                    <select name="is_active" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none">
                        <option value="1" :selected="activeMaterial.is_active == 1">Aktif Pasok</option>
                        <option value="0" :selected="activeMaterial.is_active == 0">Nonaktif / Tutup Pasokan</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition">Perbarui Detail</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
