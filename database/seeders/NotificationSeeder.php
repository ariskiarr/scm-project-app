<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Notifikasi untuk berbagai aktivitas UMKM Kebab.
     */
    public function run(): void
    {
        $pemilik    = User::where('email', 'pemilik@kebab.com')->first();
        $kasir1     = User::where('email', 'kasir@kebab.com')->first();
        $pelanggan1 = User::where('email', 'pelanggan@kebab.com')->first();

        if (! $pemilik || ! $kasir1 || ! $pelanggan1) return;

        // ── Stok Bahan Baku Menipis (ke Pemilik) ───────────────────
        $lowStockNotifications = [
            ['title' => 'Stok Daging Sapi Menipis',      'message' => 'Stok daging sapi iris tersisa 3 kg, segera lakukan pemesanan ke supplier.'],
            ['title' => 'Stok Tortilla Hampir Habis',    'message' => 'Stok tortilla/kulit kebab tersisa 20 lembar. Segera order ke Tortilla Indah.'],
            ['title' => 'Stok Keju Cheddar Menipis',     'message' => 'Stok keju cheddar slice tersisa 10 lembar. Segera lakukan re-stok.'],
            ['title' => 'Stok Minyak Goreng Menipis',    'message' => 'Stok minyak goreng tersisa 2 liter. Segera lakukan pembelian.'],
        ];

        foreach ($lowStockNotifications as $notif) {
            Notification::create([
                'user_id'  => $pemilik->id,
                'type'     => 'low_stock',
                'title'    => $notif['title'],
                'message'  => $notif['message'],
                'is_read'  => false,
            ]);
        }

        // ── Notifikasi PO (ke Pemilik) ────────────────────────────
        Notification::create([
            'user_id'  => $pemilik->id,
            'type'     => 'po_confirmed',
            'title'    => 'PO Daging Sapi Dikonfirmasi',
            'message'  => 'Purchase Order daging sapi dari Sumber Daging Sejahtera telah dikonfirmasi.',
            'is_read'  => true,
        ]);

        Notification::create([
            'user_id'  => $pemilik->id,
            'type'     => 'po_confirmed',
            'title'    => 'PO Tortilla Dikonfirmasi',
            'message'  => 'Purchase Order tortilla dari Tortilla Indah telah dikonfirmasi.',
            'is_read'  => true,
        ]);

        Notification::create([
            'user_id'  => $pemilik->id,
            'type'     => 'po_shipped',
            'title'    => 'PO Sayuran Sedang Dikirim',
            'message'  => 'Purchase Order sayuran dari Sari Sayur Segar sedang dalam perjalanan.',
            'is_read'  => false,
        ]);

        Notification::create([
            'user_id'  => $pemilik->id,
            'type'     => 'po_shipped',
            'title'    => 'PO Bumbu Sedang Dikirim',
            'message'  => 'Purchase Order bumbu dari Bumbu Khas Timur Tengah sedang dalam perjalanan.',
            'is_read'  => false,
        ]);

        Notification::create([
            'user_id'  => $pemilik->id,
            'type'     => 'po_received',
            'title'    => 'PO Daging Ayam Telah Diterima',
            'message'  => 'Purchase Order daging ayam dari Poultry Fresh Supply telah diterima dan stok sudah masuk gudang.',
            'is_read'  => true,
        ]);

        // ── Notifikasi Pesanan Baru (ke Kasir) ────────────────────
        $newOrderMessages = [
            ['title' => 'Pesanan Baru #ORD-1001', 'message' => 'Pelanggan memesan 2 Kebab Sapi Spesial dan 1 Es Teh Manis.'],
            ['title' => 'Pesanan Baru #ORD-1002', 'message' => 'Pelanggan memesan 1 Kebab Jumbo Sapi dan 1 Kentang Goreng.'],
            ['title' => 'Pesanan Baru #ORD-1003', 'message' => 'Pelanggan memesan 3 Kebab Ayam Pedas dan 2 Es Jeruk.'],
            ['title' => 'Pesanan Baru #ORD-1004', 'message' => 'Pelanggan memesan 1 Kebab Mini dan 1 Nugget Ayam.'],
            ['title' => 'Pesanan Baru #ORD-1005', 'message' => 'Pelanggan memesan 2 Kebab Sapi Keju dan 2 Lemon Tea.'],
        ];

        foreach ($newOrderMessages as $notif) {
            Notification::create([
                'user_id'  => $kasir1->id,
                'type'     => 'new_order',
                'title'    => $notif['title'],
                'message'  => $notif['message'],
                'is_read'  => false,
            ]);
        }

        // ── Notifikasi Status Pesanan (ke Pelanggan) ──────────────
        Notification::create([
            'user_id'  => $pelanggan1->id,
            'type'     => 'order_confirmed',
            'title'    => 'Pesanan #ORD-0001 Dikonfirmasi',
            'message'  => 'Pesanan Kebab Anda telah dikonfirmasi dan sedang diproses.',
            'is_read'  => true,
        ]);

        Notification::create([
            'user_id'  => $pelanggan1->id,
            'type'     => 'order_shipped',
            'title'    => 'Pesanan #ORD-0003 Sedang Dikirim',
            'message'  => 'Pesanan Kebab Anda sedang dalam perjalanan. Estimasi tiba 30 menit lagi.',
            'is_read'  => false,
        ]);

        Notification::create([
            'user_id'  => $pelanggan1->id,
            'type'     => 'order_delivered',
            'title'    => 'Pesanan #ORD-0005 Telah Sampai',
            'message'  => 'Pesanan Kebab Anda telah sampai. Selamat menikmati!',
            'is_read'  => true,
        ]);

        // Notifikasi promo (ke pelanggan)
        Notification::create([
            'user_id'  => $pelanggan1->id,
            'type'     => 'general',
            'title'    => 'Promo Spesial Akhir Pekan!',
            'message'  => 'Beli 2 Kebab Sapi Spesial gratis 1 Es Teh Manis. Hanya akhir pekan ini!',
            'is_read'  => true,
        ]);

        $this->command->info('✅ Notifikasi berhasil dibuat');
    }
}
