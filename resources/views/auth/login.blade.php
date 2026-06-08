@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 py-8 bg-amber-50/30">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white/85 backdrop-blur-md border border-slate-100 shadow-2xl rounded-3xl p-8">
            <div class="text-center mb-6">
                {{-- <div class="mx-auto w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-red-500 flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div> --}}
                <h2 class="mt-3 text-xl font-bold tracking-tight text-slate-800">Selamat Datang Kembali</h2>
                <p class="mt-1 text-xs text-slate-500">Masuk untuk mengelola supply chain & pesanan Kebab Anda</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <label for="remember" class="ml-2 block text-xs text-slate-500">Ingat saya</label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/10 hover:bg-amber-600 focus:outline-none transition-all duration-150">
                        Masuk Sistem
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs">
                <span class="text-slate-500">Belum punya akun Pelanggan? </span>
                <a href="{{ route('register') }}" class="font-bold text-amber-600 hover:text-amber-700">Daftar Sekarang</a>
            </div>

            <!-- Demo Logins -->
            <div class="mt-8 border-t border-slate-100 pt-6">
                <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Klik untuk Login Demo</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="setDemo('pemilik@kebab.com')" class="flex items-center justify-center gap-1.5 rounded-lg border border-amber-100 bg-amber-50/50 px-2.5 py-1.5 text-[11px] font-semibold text-amber-700 hover:bg-amber-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                        Pemilik
                    </button>
                    <button type="button" onclick="setDemo('kasir@kebab.com')" class="flex items-center justify-center gap-1.5 rounded-lg border border-blue-100 bg-blue-50/50 px-2.5 py-1.5 text-[11px] font-semibold text-blue-700 hover:bg-blue-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                        Kasir
                    </button>
                    <button type="button" onclick="setDemo('pemasok1@kebab.com')" class="flex items-center justify-center gap-1.5 rounded-lg border border-purple-100 bg-purple-50/50 px-2.5 py-1.5 text-[11px] font-semibold text-purple-700 hover:bg-purple-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Pemasok
                    </button>
                    <button type="button" onclick="setDemo('kurir@kebab.com')" class="flex items-center justify-center gap-1.5 rounded-lg border border-teal-100 bg-teal-50/50 px-2.5 py-1.5 text-[11px] font-semibold text-teal-700 hover:bg-teal-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                        Kurir
                    </button>
                    <button type="button" onclick="setDemo('pelanggan@kebab.com')" class="col-span-2 flex items-center justify-center gap-1.5 rounded-lg border border-slate-100 bg-slate-50 px-2.5 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        Pelanggan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setDemo(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }
</script>
@endsection
