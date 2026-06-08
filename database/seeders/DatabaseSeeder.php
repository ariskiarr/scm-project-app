<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed seluruh database UMKM Kebab.
     * Urutan pemanggilan seeder diperhatikan agar tidak terjadi error foreign key.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,               // 1. User (pemilik, kasir, kurir, pelanggan, pemasok)
            SupplierSeeder::class,            // 2. Pemasok bahan baku
            RawMaterialSeeder::class,         // 3. Bahan baku
            SupplierRawMaterialSeeder::class, // 4. Relasi supplier <-> bahan baku
            ProductSeeder::class,              // 5. Produk yang dijual
            PurchaseOrderSeeder::class,       // 6. Purchase Order + items + delivery updates
            CustomerOrderSeeder::class,       // 7. Pesanan pelanggan + items + delivery updates
            StockMutationSeeder::class,       // 8. Mutasi stok bahan baku
            NotificationSeeder::class,        // 9. Notifikasi
            DailySalesSummarySeeder::class,   // 10. Rekap penjualan harian
        ]);

        $this->command->info('✅ Database UMKM Kebab Berhasil di-Seed!');
        $this->command->newLine();
        $this->command->info('📋 Akun Demo untuk Login:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Pemilik',   'pemilik@kebab.com',   'password'],
                ['Kasir',     'kasir@kebab.com',     'password'],
                ['Kurir',     'kurir@kebab.com',     'password'],
                ['Pelanggan', 'pelanggan@kebab.com', 'password'],
            ]
        );
    }
}
