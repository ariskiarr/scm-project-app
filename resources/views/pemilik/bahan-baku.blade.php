@extends('layouts.app')

@section('title', 'Manajemen Bahan Baku')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openAddModal: false, openEditModal: false, activeMaterial: {} }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Bahan Baku</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola ketersediaan bahan baku, harga beli, dan batas minimum stok.</p>
        </div>
        <button @click="openAddModal = true" class="rounded-xl  bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-500/10 hover:from-amber-600 hover:to-red-600 transition flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            Tambah Bahan Baku
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Kode / Nama</th>
                        <th class="py-4 px-6">Harga Satuan</th>
                        <th class="py-4 px-6 text-center">Stok Saat Ini</th>
                        <th class="py-4 px-6 text-center">Batas Min</th>
                        <th class="py-4 px-6">Status Stok</th>
                        <th class="py-4 px-6">Deskripsi</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                    @forelse($materials as $mat)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 block">{{ $mat->name }}</span>
                                <span class="text-2xs text-slate-400 font-mono block">{{ $mat->code }}</span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                Rp {{ number_format($mat->price_per_unit, 0, ',', '.') }} <span class="text-2xs text-slate-400 font-normal">/ {{ $mat->unit }}</span>
                            </td>
                            <td class="py-4 px-6 text-center font-bold text-slate-800">
                                {{ floatval($mat->current_stock) }} <span class="text-xs font-normal text-slate-400">{{ $mat->unit }}</span>
                            </td>
                            <td class="py-4 px-6 text-center font-medium text-slate-500">
                                {{ floatval($mat->minimum_stock) }} <span class="text-xs font-normal text-slate-400">{{ $mat->unit }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($mat->is_low_stock)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-2xs font-bold text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.34c-.22.38-.22.86 0 1.24.22.38.62.62 1.04.62h16.94c.42 0 .82-.24 1.04-.62.22-.38.22-.86 0-1.24l-8.3-14.34c-.22-.38-.62-.62-1.04-.62s-.82.24-1.04.62z" /></svg>
                                        Menipis
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-2xs font-bold text-emerald-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Aman
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400 max-w-xs truncate">
                                {{ $mat->description ?: '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button @click="activeMaterial = {{ $mat }}; openEditModal = true" class="text-amber-600 hover:text-amber-700 font-semibold text-xs px-2.5 py-1.5 hover:bg-amber-50 rounded-lg transition">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                Tidak ada data bahan baku.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add Bahan Baku -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openAddModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Tambah Bahan Baku Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form action="{{ route('pemilik.bahan-baku.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Kode Bahan Baku</label>
                        <input type="text" name="code" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="BB-BEEF">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Nama Bahan</label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Daging Sapi Kebab">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Satuan Beli</label>
                        <input type="text" name="unit" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="kg">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Stok Awal</label>
                        <input type="number" step="0.01" name="current_stock" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="10">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Batas Min</label>
                        <input type="number" step="0.01" name="minimum_stock" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="2">
                    </div>
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Estimasi Harga Beli Satuan (Rp)</label>
                    <input type="number" name="price_per_unit" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="95000">
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Daging sapi berkualitas impor..."></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-red-500 text-sm font-semibold text-white shadow-md hover:from-amber-600 hover:to-red-600 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Bahan Baku -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openEditModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Edit Bahan Baku</h3>
                <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="`{{ url('pemilik/bahan-baku') }}/${activeMaterial.id}`" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Kode Bahan Baku</label>
                        <input type="text" name="code" :value="activeMaterial.code" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Nama Bahan</label>
                        <input type="text" name="name" :value="activeMaterial.name" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Satuan Beli</label>
                        <input type="text" name="unit" :value="activeMaterial.unit" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Batas Min</label>
                        <input type="number" step="0.01" name="minimum_stock" :value="activeMaterial.minimum_stock" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Harga Beli Satuan (Rp)</label>
                    <input type="number" name="price_per_unit" :value="activeMaterial.price_per_unit" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Deskripsi</label>
                    <textarea name="description" rows="3" :value="activeMaterial.description" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"></textarea>
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Status Aktif</label>
                    <select name="is_active" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="1" :selected="activeMaterial.is_active == 1">Aktif</option>
                        <option value="0" :selected="activeMaterial.is_active == 0">Nonaktif</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-red-500 text-sm font-semibold text-white shadow-md hover:from-amber-600 hover:to-red-600 transition">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
