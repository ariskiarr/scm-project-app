<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemilikController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\PelangganController;

// Public Guest Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Notification global route
    Route::post('/notifications/read-all', [PemilikController::class, 'readAllNotifications'])->name('notifications.read-all');

    // 1. Pemilik Usaha Routes
    Route::prefix('pemilik')->middleware(['role:pemilik'])->group(function () {
        Route::get('/dashboard', [PemilikController::class, 'dashboard'])->name('pemilik.dashboard');

        // Bahan Baku (Inventory)
        Route::get('/bahan-baku', [PemilikController::class, 'bahanBaku'])->name('pemilik.bahan-baku');
        Route::post('/bahan-baku', [PemilikController::class, 'storeBahanBaku'])->name('pemilik.bahan-baku.store');
        Route::put('/bahan-baku/{id}', [PemilikController::class, 'updateBahanBaku'])->name('pemilik.bahan-baku.update');

        // Pemasok (Suppliers)
        Route::get('/pemasok', [PemilikController::class, 'pemasok'])->name('pemilik.pemasok');
        Route::post('/pemasok', [PemilikController::class, 'storePemasok'])->name('pemilik.pemasok.store');
        Route::put('/pemasok/{id}', [PemilikController::class, 'updatePemasok'])->name('pemilik.pemasok.update');
        Route::post('/pemasok/{id}/link', [PemilikController::class, 'linkRawMaterial'])->name('pemilik.pemasok.link');
        Route::delete('/pemasok/{supplierId}/unlink/{materialId}', [PemilikController::class, 'unlinkRawMaterial'])->name('pemilik.pemasok.unlink');

        // Purchase Orders (PO)
        Route::get('/purchase-orders', [PemilikController::class, 'purchaseOrders'])->name('pemilik.purchase-orders');
        Route::get('/purchase-orders/create', [PemilikController::class, 'createPO'])->name('pemilik.purchase-orders.create');
        Route::post('/purchase-orders', [PemilikController::class, 'storePO'])->name('pemilik.purchase-orders.store');
        Route::post('/purchase-orders/{id}/cancel', [PemilikController::class, 'cancelPO'])->name('pemilik.purchase-orders.cancel');
        Route::post('/purchase-orders/{id}/receive', [PemilikController::class, 'receivePO'])->name('pemilik.purchase-orders.receive');

        // Produk (Menu)
        Route::get('/produk', [PemilikController::class, 'produk'])->name('pemilik.produk');
        Route::post('/produk', [PemilikController::class, 'storeProduk'])->name('pemilik.produk.store');
        Route::put('/produk/{id}', [PemilikController::class, 'updateProduk'])->name('pemilik.produk.update');
        Route::delete('/produk/{id}', [PemilikController::class, 'destroyProduk'])->name('pemilik.produk.destroy');

        // Laporan Penjualan
        Route::get('/laporan', [PemilikController::class, 'laporan'])->name('pemilik.laporan');

        // Manajemen Akun Pengguna
        Route::get('/akun', [PemilikController::class, 'akun'])->name('pemilik.akun');
        Route::post('/akun', [PemilikController::class, 'storeAkun'])->name('pemilik.akun.store');
        Route::put('/akun/{id}', [PemilikController::class, 'updateAkun'])->name('pemilik.akun.update');
    });

    // 2. Kasir Routes
    Route::prefix('kasir')->middleware(['role:kasir'])->group(function () {
        Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');

        // POS Cashier Input
        Route::get('/transaksi/create', [KasirController::class, 'transaksiCreate'])->name('kasir.transaksi.create');
        Route::post('/transaksi', [KasirController::class, 'transaksiStore'])->name('kasir.transaksi.store');

        // Customer Order Manager
        Route::get('/pesanan', [KasirController::class, 'pesanan'])->name('kasir.pesanan');
        Route::post('/pesanan/{id}/update-status', [KasirController::class, 'pesananUpdateStatus'])->name('kasir.pesanan.update-status');

        // Rekap Transaksi Harian
        Route::get('/rekap', [KasirController::class, 'rekap'])->name('kasir.rekap');
        Route::post('/rekap/generate', [KasirController::class, 'rekapGenerate'])->name('kasir.rekap.generate');
    });

    // 3. Pemasok Routes
    Route::prefix('pemasok')->middleware(['role:pemasok'])->group(function () {
        Route::get('/dashboard', [PemasokController::class, 'dashboard'])->name('pemasok.dashboard');
        Route::post('/purchase-orders/{id}/confirm', [PemasokController::class, 'confirmPO'])->name('pemasok.purchase-orders.confirm');
        Route::post('/purchase-orders/{id}/pengiriman', [PemasokController::class, 'updatePengiriman'])->name('pemasok.purchase-orders.pengiriman');

        // Raw Material availability & price contract management
        Route::get('/stok', [PemasokController::class, 'stok'])->name('pemasok.stok');
        Route::put('/stok/{materialId}', [PemasokController::class, 'updateStok'])->name('pemasok.stok.update');
    });

    // 4. Kurir Routes
    Route::prefix('kurir')->middleware(['role:kurir'])->group(function () {
        Route::get('/dashboard', [KurirController::class, 'dashboard'])->name('kurir.dashboard');
        Route::post('/pengiriman/{id}/update', [KurirController::class, 'updatePengiriman'])->name('kurir.pengiriman.update');
    });

    // 5. Pelanggan Routes
    Route::prefix('pelanggan')->middleware(['role:pelanggan'])->group(function () {
        Route::get('/dashboard', [PelangganController::class, 'dashboard'])->name('pelanggan.dashboard');

        // Checkout & Pemesanan
        Route::get('/checkout', [PelangganController::class, 'checkout'])->name('pelanggan.checkout');
        Route::post('/order', [PelangganController::class, 'storeOrder'])->name('pelanggan.order');

        // Tracking & Riwayat
        Route::get('/tracking/{id}', [PelangganController::class, 'tracking'])->name('pelanggan.tracking');
        Route::get('/riwayat', [PelangganController::class, 'riwayat'])->name('pelanggan.riwayat');
    });
});
