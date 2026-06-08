@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{ openAddModal: false, openEditModal: false, activeUser: {} }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Akun Pengguna</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola hak akses pengguna sistem (Pemilik, Kasir, Kurir, Pemasok).</p>
        </div>
        <button @click="openAddModal = true" class="rounded-xl bg-gradient-to-r from-amber-500 to-red-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-500/10 hover:from-amber-600 hover:to-red-600 transition flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg> Tambah Pengguna Baru
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Pengguna</th>
                        <th class="py-4 px-6">Email / Kontak</th>
                        <th class="py-4 px-6">Role</th>
                        <th class="py-4 px-6">Alamat</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 block">{{ $user->name }}</span>
                                <span class="text-3xs text-slate-400 block">ID: #{{ $user->id }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-700 block">{{ $user->email }}</span>
                                <span class="text-xs text-slate-400 block">Telp: {{ $user->phone }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-2xs font-bold uppercase tracking-wider
                                    @if($user->role === 'pemilik') bg-amber-50 text-amber-700
                                    @elseif($user->role === 'kasir') bg-blue-50 text-blue-700
                                    @elseif($user->role === 'kurir') bg-teal-50 text-teal-700
                                    @elseif($user->role === 'pemasok') bg-purple-50 text-purple-700
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500 max-w-xs truncate">
                                {{ $user->address }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($user->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-2xs font-bold text-emerald-700">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-2xs font-bold text-red-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button @click="activeUser = {{ $user }}; openEditModal = true" class="text-amber-600 hover:text-amber-700 font-semibold text-xs px-2.5 py-1.5 hover:bg-amber-50 rounded-lg transition">
                                    Edit Akun
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add Akun -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openAddModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Tambah Pengguna Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form action="{{ route('pemilik.akun.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="Ahmad Budi">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Email Login</label>
                        <input type="email" name="email" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="budi@email.com">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">No HP / WhatsApp</label>
                        <input type="text" name="phone" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="0812xxxx">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Role Akses</label>
                        <select name="role" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                            <option value="pemilik">Pemilik Usaha</option>
                            <option value="kasir">Kasir</option>
                            <option value="kurir">Kurir</option>
                            <option value="pemasok">Pemasok</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                    <textarea name="address" rows="2" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="Jl. Raya No. 12..."></textarea>
                </div>

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Kata Sandi Default</label>
                    <input type="password" name="password" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="Masukkan password">
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-50">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-red-500 text-sm font-semibold text-white shadow-md hover:from-amber-600 hover:to-red-600 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Akun -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-sm" style="display: none;">
        <div @click.away="openEditModal = false" class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Edit Pengguna</h3>
                <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="`{{ url('pemilik/akun') }}/${activeUser.id}`" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" :value="activeUser.name" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Email Login</label>
                        <input type="email" name="email" :value="activeUser.email" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">No HP / WhatsApp</label>
                        <input type="text" name="phone" :value="activeUser.phone" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Role Akses</label>
                        <select name="role" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                            <option value="pemilik" :selected="activeUser.role === 'pemilik'">Pemilik Usaha</option>
                            <option value="kasir" :selected="activeUser.role === 'kasir'">Kasir</option>
                            <option value="kurir" :selected="activeUser.role === 'kurir'">Kurir</option>
                            <option value="pemasok" :selected="activeUser.role === 'pemasok'">Pemasok</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                    <textarea name="address" rows="2" required :value="activeUser.address" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Ganti Kata Sandi (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="Masukkan password baru">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Status Akun</label>
                        <select name="is_active" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                            <option value="1" :selected="activeUser.is_active == 1">Aktif</option>
                            <option value="0" :selected="activeUser.is_active == 0">Nonaktif</option>
                        </select>
                    </div>
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
