@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openAddModal: false, openEditModal: false, activeProduct: {} }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Produk</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola menu kebab, harga jual, stok produk, dan ketersediaan.</p>
        </div>
        <button @click="openAddModal = true" class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            Tambah Produk
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Kode / Nama</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6 text-right">Harga Jual</th>
                        <th class="py-4 px-6 text-center">Stok</th>
                        <th class="py-4 px-6 text-center">Tersedia</th>
                        <th class="py-4 px-6">Deskripsi</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 block">{{ $prod->name }}</span>
                                <span class="text-2xs text-slate-400 font-mono block">{{ $prod->code }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs text-slate-500">{{ $prod->category ?: '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-right font-semibold text-slate-800">
                                Rp {{ number_format($prod->price, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center font-bold text-slate-800">
                                {{ floatval($prod->stock) }} <span class="text-xs font-normal text-slate-400">{{ $prod->unit }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($prod->is_available)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-2xs font-bold text-emerald-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-2xs font-bold text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                        Tidak
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400 max-w-xs truncate">
                                {{ $prod->description ?: '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="activeProduct = {{ $prod }}; openEditModal = true" class="text-amber-600 hover:text-amber-700 font-semibold text-xs px-2.5 py-1.5 hover:bg-amber-50 rounded-lg transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('pemilik.produk.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $prod->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs px-2.5 py-1.5 hover:bg-red-50 rounded-lg transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                Tidak ada data produk. Tambah produk baru untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add Produk -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openAddModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Tambah Produk Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form action="{{ route('pemilik.produk.store') }}" method="POST" class="p-6 space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Kode Produk</label>
                        <input type="text" name="code" required class="mt-0.5 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="KEBAB-BEEF">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Nama Produk</label>
                        <input type="text" name="name" required class="mt-0.5 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Kebab Sapi Spesial">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Kategori</label>
                        <input type="text" name="category" class="mt-0.5 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Makanan">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Harga (Rp)</label>
                        <input type="number" name="price" required step="0.01" class="mt-0.5 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="25000">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Satuan</label>
                        <input type="text" name="unit" required class="mt-0.5 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="pcs">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Stok</label>
                        <input type="number" name="stock" required step="0.01" class="mt-0.5 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="50">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500 flex items-center gap-2 mt-3">
                            <input type="checkbox" name="is_available" value="1" checked class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                            <span class="text-xs text-slate-600">Tersedia untuk dijual</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="2" class="mt-0.5 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Kebab sapi dengan daging segar dan sayuran renyah..."></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openAddModal = false" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-500 text-xs font-semibold text-white shadow-md hover:bg-amber-600 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Produk -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openEditModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Edit Produk</h3>
                <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="`{{ url('pemilik/produk') }}/${activeProduct.id}`" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Kode Produk</label>
                        <input type="text" name="code" :value="activeProduct.code" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Nama Produk</label>
                        <input type="text" name="name" :value="activeProduct.name" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Kategori</label>
                        <input type="text" name="category" :value="activeProduct.category" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Harga Jual (Rp)</label>
                        <input type="number" name="price" :value="activeProduct.price" required step="0.01" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Satuan</label>
                        <input type="text" name="unit" :value="activeProduct.unit" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Stok</label>
                        <input type="number" name="stock" :value="activeProduct.stock" required step="0.01" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-3xs font-semibold text-slate-500">Status Tersedia</label>
                        <select name="is_available" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <option value="1" :selected="activeProduct.is_available == 1">Aktif (Tersedia)</option>
                            <option value="0" :selected="activeProduct.is_available == 0">Nonaktif (Tidak Tersedia)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-3xs font-semibold text-slate-500">Deskripsi</label>
                    <textarea name="description" rows="3" :value="activeProduct.description" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
