<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kebab Berkah') - UMKM Kebab SCM</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    @yield('styles')
</head>
<body class="h-full flex flex-col">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-slate-100 sticky top-0 z-40 shadow-sm backdrop-blur-md bg-white/90">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-600 flex items-center justify-center text-white font-bold text-xl shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-lg text-slate-800 tracking-tight block">Kebab Berkah</span>
                        <span class="text-xs text-amber-600 font-semibold uppercase tracking-wider -mt-1 block">SCM Portal</span>
                    </div>
                </div>

                <!-- Navigation Links based on role -->
                <nav class="hidden md:flex items-center gap-6">
                    @auth
                        @if(auth()->user()->isPemilik())
                            <a href="{{ route('pemilik.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('pemilik.dashboard') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Dashboard</a>
                            <a href="{{ route('pemilik.bahan-baku') }}" class="text-sm font-medium {{ request()->routeIs('pemilik.bahan-baku') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Bahan Baku</a>
                            <a href="{{ route('pemilik.pemasok') }}" class="text-sm font-medium {{ request()->routeIs('pemilik.pemasok') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Pemasok</a>
                            <a href="{{ route('pemilik.produk') }}" class="text-sm font-medium {{ request()->routeIs('pemilik.produk') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Produk</a>
                            <a href="{{ route('pemilik.purchase-orders') }}" class="text-sm font-medium {{ request()->routeIs('pemilik.purchase-orders') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Purchase Order</a>
                            <a href="{{ route('pemilik.laporan') }}" class="text-sm font-medium {{ request()->routeIs('pemilik.laporan') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Laporan</a>
                            <a href="{{ route('pemilik.akun') }}" class="text-sm font-medium {{ request()->routeIs('pemilik.akun') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">User Akun</a>
                        @elseif(auth()->user()->isKasir())
                            <a href="{{ route('kasir.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('kasir.dashboard') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Dashboard</a>
                            <a href="{{ route('kasir.transaksi.create') }}" class="text-sm font-medium {{ request()->routeIs('kasir.transaksi.create') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Input Transaksi</a>
                            <a href="{{ route('kasir.pesanan') }}" class="text-sm font-medium {{ request()->routeIs('kasir.pesanan') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Pesanan Pelanggan</a>
                            <a href="{{ route('kasir.rekap') }}" class="text-sm font-medium {{ request()->routeIs('kasir.rekap') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Rekap Harian</a>
                        @elseif(auth()->user()->isPemasok())
                            <a href="{{ route('pemasok.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('pemasok.dashboard') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Konfirmasi PO</a>
                            <a href="{{ route('pemasok.stok') }}" class="text-sm font-medium {{ request()->routeIs('pemasok.stok') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Atur Stok & Harga</a>
                        @elseif(auth()->user()->isKurir())
                            <a href="{{ route('kurir.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('kurir.dashboard') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Pengiriman</a>
                        @elseif(auth()->user()->isPelanggan())
                            <a href="{{ route('pelanggan.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('pelanggan.dashboard') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Beli Kebab</a>
                            <a href="{{ route('pelanggan.riwayat') }}" class="text-sm font-medium {{ request()->routeIs('pelanggan.riwayat') ? 'text-amber-600' : 'text-slate-600 hover:text-slate-900' }}">Riwayat Pesanan</a>
                        @endif
                    @endauth
                </nav>

                <!-- Right Menu: Notifications & Profile -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- Notification Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            @php
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                                $notifications = auth()->user()->unreadNotifications()->latest()->take(5)->get();
                            @endphp
                            <button @click="open = !open" class="relative p-2 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($unreadCount > 0)
                                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white ring-2 ring-white">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <!-- Dropdown List -->
                            <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-80 rounded-xl bg-white py-2 shadow-xl border border-slate-100 ring-1 ring-black/5 z-50">
                                <div class="px-4 py-2 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 rounded-t-xl">
                                    <span class="font-bold text-slate-800 text-sm">Notifikasi</span>
                                    @if($unreadCount > 0)
                                        <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-amber-600 hover:text-amber-700 font-semibold">Tandai semua dibaca</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse($notifications as $notif)
                                        <div class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition">
                                            <div class="flex justify-between items-start gap-1">
                                                <span class="font-semibold text-xs text-slate-800 block">{{ $notif->title }}</span>
                                                <span class="text-[9px] text-slate-400 shrink-0">{{ $notif->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1 leading-normal">{{ $notif->message }}</p>
                                        </div>
                                    @empty
                                        <div class="px-4 py-8 text-center">
                                            <p class="text-xs text-slate-400">Tidak ada notifikasi baru</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- User Info & Logout -->
                        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                            <div class="hidden sm:block text-right">
                                <span class="block text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                                <span class="block text-[10px] text-amber-600 font-bold uppercase tracking-wider">{{ auth()->user()->role }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition" title="Log Keluar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-amber-700 transition">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Nav Bar (bottom) -->
    @auth
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 z-40 px-4 py-2 flex justify-around shadow-lg">
        @if(auth()->user()->isPemilik())
            <a href="{{ route('pemilik.dashboard') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pemilik.dashboard') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('pemilik.bahan-baku') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pemilik.bahan-baku') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span>Stok</span>
            </a>
            <a href="{{ route('pemilik.produk') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pemilik.produk') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>Produk</span>
            </a>
            <a href="{{ route('pemilik.purchase-orders') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pemilik.purchase-orders') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                <span>PO</span>
            </a>
            <a href="{{ route('pemilik.laporan') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pemilik.laporan') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Laporan</span>
            </a>
        @elseif(auth()->user()->isKasir())
            <a href="{{ route('kasir.dashboard') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('kasir.dashboard') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('kasir.transaksi.create') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('kasir.transaksi.create') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>POS</span>
            </a>
            <a href="{{ route('kasir.pesanan') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('kasir.pesanan') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                <span>Order</span>
            </a>
            <a href="{{ route('kasir.rekap') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('kasir.rekap') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Rekap</span>
            </a>
        @elseif(auth()->user()->isPemasok())
            <a href="{{ route('pemasok.dashboard') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pemasok.dashboard') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                <span>PO Masuk</span>
            </a>
            <a href="{{ route('pemasok.stok') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pemasok.stok') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span>Atur Stok</span>
            </a>
        @elseif(auth()->user()->isKurir())
            <a href="{{ route('kurir.dashboard') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('kurir.dashboard') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-1m0 0l2 1m-2-1v2a1 1 0 001 1h14a1 1 0 001-1v-2m0 0l2 1m-2-1l-2 1" /></svg>
                <span>Pengiriman</span>
            </a>
        @elseif(auth()->user()->isPelanggan())
            <a href="{{ route('pelanggan.dashboard') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pelanggan.dashboard') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>Menu</span>
            </a>
            <a href="{{ route('pelanggan.riwayat') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('pelanggan.riwayat') ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Pesanan Saya</span>
            </a>
        @endif
    </div>
    @endauth

    <!-- Main Content Area -->
    <main class="flex-1 pb-16 md:pb-6">
        @if(session('success'))
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-xl bg-red-50 border border-red-100 p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-bold text-red-800">Mohon perbaiki kesalahan berikut:</p>
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-xs text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-6 text-center text-xs text-slate-400 mt-auto hidden md:block">
        <p>&copy; 2026 Kebab Berkah. All rights reserved.</p>
    </footer>

    <!-- Alpine.js CDN for interactive dropdowns, modals, and tabs -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('scripts')
</body>
</html>

