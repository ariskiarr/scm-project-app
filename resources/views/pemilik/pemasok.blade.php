@extends('layouts.app')

@section('title', 'Manajemen Pemasok')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openAddModal: false, openLinkModal: false, activeSupplierId: null }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Pemasok</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola data pemasok bahan baku, harga kontrak, dan batas minimum pemesanan.</p>
        </div>
        <button @click="openAddModal = true" class="rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-500/10 transition flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            Tambah Pemasok Baru
        </button>
    </div>

    <!-- Suppliers List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($suppliers as $sup)
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-50">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold text-slate-800">{{ $sup->company_name }}</h2>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-3xs font-bold uppercase tracking-wider bg-purple-50 text-purple-700">
                                Pemasok
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">PJ: <span class="font-semibold text-slate-600">{{ $sup->contact_person }}</span> | Telepon: <span class="font-semibold text-slate-600">{{ $sup->phone }}</span> | Email: <span class="font-semibold text-slate-600">{{ $sup->email ?: '-' }}</span></p>
                        <p class="text-xs text-slate-400 mt-0.5">Alamat: <span class="text-slate-600">{{ $sup->address }}</span></p>
                        @if($sup->bank_name)
                            <p class="text-xs text-slate-400 mt-0.5">Rekening: <span class="text-slate-600">{{ $sup->bank_name }} - {{ $sup->bank_account_number }} (a.n. {{ $sup->bank_account_name }})</span></p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <button @click="activeSupplierId = {{ $sup->id }}; openLinkModal = true" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-3.5 py-2 rounded-xl transition shadow-sm shadow-amber-500/10 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            Kaitkan Bahan Baku
                        </button>
                    </div>
                </div>

                <!-- Linked Raw Materials Pivot -->
                <div class="mt-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Katalog Bahan Baku yang Disediakan:</h3>
                    @if($sup->rawMaterials->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-slate-400 text-3xs font-bold uppercase tracking-wider border-b border-slate-50">
                                        <th class="pb-2">Nama Bahan Baku</th>
                                        <th class="pb-2 text-right">Harga Jual Pemasok</th>
                                        <th class="pb-2 text-center">Min. Pemesanan</th>
                                        <th class="pb-2 text-center">Stok di Pemasok</th>
                                        <th class="pb-2 text-center">Lead Time</th>
                                        <th class="pb-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs text-slate-600 divide-y divide-slate-50">
                                    @foreach($sup->rawMaterials as $mat)
                                        <tr>
                                            <td class="py-2.5">
                                                <span class="font-bold text-slate-800">{{ $mat->name }}</span>
                                                <span class="text-3xs text-slate-400 font-mono block">{{ $mat->code }}</span>
                                            </td>
                                            <td class="py-2.5 text-right font-semibold text-slate-800">
                                                Rp {{ number_format($mat->pivot->price_per_unit, 0, ',', '.') }} / {{ $mat->unit }}
                                            </td>
                                            <td class="py-2.5 text-center font-medium">
                                                {{ floatval($mat->pivot->minimum_order_qty) }} {{ $mat->unit }}
                                            </td>
                                            <td class="py-2.5 text-center">
                                                {{ floatval($mat->pivot->available_stock) }} {{ $mat->unit }}
                                            </td>
                                            <td class="py-2.5 text-center text-slate-400">
                                                {{ $mat->pivot->lead_time_days }} hari
                                            </td>
                                            <td class="py-2.5 text-center">
                                                <form action="{{ route('pemilik.pemasok.unlink', [$sup->id, $mat->id]) }}" method="POST" class="inline" onsubmit="return confirm('Lepas kaitan bahan baku ini dari pemasok?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 hover:bg-red-50 text-2xs px-2 py-1 rounded-md transition font-semibold">
                                                        Hapus Kaitan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-slate-50 p-4 text-center rounded-2xl">
                            <p class="text-xs text-slate-400">Belum ada bahan baku yang dikaitkan ke pemasok ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Belum ada pemasok terdaftar.
            </div>
        @endforelse
    </div>

    <!-- Modal Add Pemasok -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openAddModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Tambah Pemasok Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form action="{{ route('pemilik.pemasok.store') }}" method="POST" class="p-6 space-y-4" x-data="{ create_user: '1' }">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Nama Perusahaan / Toko</label>
                        <input type="text" name="company_name" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="CV. Daging Segar Mandiri">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Contact Person (PJ)</label>
                        <input type="text" name="contact_person" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Bp. Hermawan">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">No HP / WhatsApp</label>
                        <input type="text" name="phone" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="0812345678">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Email Kontak (Opsional)</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="kontak@dagingsegar.com">
                    </div>
                </div>

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Alamat Pemasok</label>
                    <textarea name="address" rows="2" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Kawasan Industri Pulogadung..."></textarea>
                </div>

                <div class="grid grid-cols-3 gap-2 border-t border-slate-50 pt-3">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Nama Bank</label>
                        <input type="text" name="bank_name" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="BCA">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">No Rekening</label>
                        <input type="text" name="bank_account_number" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="84201xxxx">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Pemilik Rekening</label>
                        <input type="text" name="bank_account_name" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="CV Daging Segar">
                    </div>
                </div>

                <!-- Account Creation Section -->
                <div class="bg-amber-50/50 p-4 border border-amber-100 rounded-2xl mt-4">
                    <label class="block text-xs font-bold text-slate-700">Akun Pengguna untuk Pemasok</label>
                    <div class="mt-2 flex gap-4 text-xs">
                        <label class="inline-flex items-center">
                            <input type="radio" name="create_user" value="1" x-model="create_user" class="text-amber-600 focus:ring-amber-500">
                            <span class="ml-1">Buat Akun Login Baru</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="create_user" value="0" x-model="create_user" class="text-amber-600 focus:ring-amber-500">
                            <span class="ml-1">Hubungkan Akun Pemasok Terdaftar</span>
                        </label>
                    </div>

                    <!-- Div Buat Akun Baru -->
                    <div class="mt-3 grid grid-cols-2 gap-3" x-show="create_user == '1'">
                        <div>
                            <label class="block text-3xs font-bold text-slate-500 uppercase tracking-wider">Email Login</label>
                            <input type="email" name="user_email" :required="create_user == '1'" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none" placeholder="login@pemasok.com">
                        </div>
                        <div>
                            <label class="block text-3xs font-bold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
                            <input type="password" name="user_password" :required="create_user == '1'" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none" placeholder="••••••">
                        </div>
                    </div>

                    <!-- Div Hubungkan Akun Terdaftar -->
                    <div class="mt-3" x-show="create_user == '0'" style="display: none;">
                        <label class="block text-3xs font-bold text-slate-500 uppercase tracking-wider">Pilih Akun Pemasok</label>
                        <select name="user_id" :required="create_user == '0'" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none">
                            <option value="">-- Pilih Akun --</option>
                            @foreach($pemasokUsers as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-sm font-semibold text-white shadow-md transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Link Bahan Baku -->
    <div x-show="openLinkModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openLinkModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Kaitkan Bahan Baku</h3>
                <button @click="openLinkModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="`{{ url('pemilik/pemasok') }}/${activeSupplierId}/link`" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Pilih Bahan Baku</label>
                    <select name="raw_material_id" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach($materials as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->name }} ({{ $mat->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Harga Jual Pemasok (Rp)</label>
                        <input type="number" name="price_per_unit" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="95000">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Min. Order Qty</label>
                        <input type="number" step="0.01" name="minimum_order_qty" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="5">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Stok di Pemasok</label>
                        <input type="number" step="0.01" name="available_stock" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="100">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Lead Time (Hari)</label>
                        <input type="number" name="lead_time_days" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="1">
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openLinkModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-sm font-semibold text-white shadow-md transition">Simpan Kaitan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
