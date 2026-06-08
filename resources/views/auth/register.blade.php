@extends('layouts.app')

@section('title', 'Daftar Akun Pelanggan')

@section('content')
<div class="min-h-[85vh] flex flex-col items-center justify-center px-4 py-8 bg-amber-50/30">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white/85 backdrop-blur-md border border-slate-100 shadow-2xl rounded-3xl p-8">
            <div class="text-center mb-6">
                <!-- <div class="mx-auto w-14 h-14 rounded-2xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div> -->
                <h2 class="mt-3 text-xl font-bold tracking-tight text-slate-800">Daftar Akun Baru</h2>
                <p class="mt-1 text-xs text-slate-500">Dapatkan kebab lezat langsung ke tempat Anda</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="Ahmad Fauzi">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="ahmad@email.com">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nomor HP / WhatsApp</label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="08123456789">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Alamat Pengiriman</label>
                    <div class="mt-1">
                        <textarea id="address" name="address" rows="3" required
                            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="Jl. Anggrek No. 12, Kel. Harapan..."></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required
                                class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                                placeholder="••••••••">
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Konfirmasi Sandi</label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/10 hover:bg-amber-600 focus:outline-none transition-all duration-150">
                        Daftar Sebagai Pelanggan
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs">
                <span class="text-slate-500">Sudah memiliki akun? </span>
                <a href="{{ route('login') }}" class="font-bold text-amber-600 hover:text-amber-700">Masuk Sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection
