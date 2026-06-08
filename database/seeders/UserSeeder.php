<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Akun tetap dan user tambahan untuk UMKM Kebab.
     */
    public function run(): void
    {
        // ── Akun Tetap ─────────────────────────────────────────────
        User::create([
            'name'      => 'Ahmad Kebab Berkah',
            'email'     => 'pemilik@kebab.com',
            'password'  => Hash::make('password'),
            'phone'     => '081234567890',
            'address'   => 'Jl. Merdeka No. 15, Jakarta',
            'role'      => 'pemilik',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Siti Rahayu',
            'email'     => 'kasir@kebab.com',
            'password'  => Hash::make('password'),
            'phone'     => '081234567891',
            'address'   => 'Jl. Sudirman No. 28, Jakarta',
            'role'      => 'kasir',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'kurir@kebab.com',
            'password'  => Hash::make('password'),
            'phone'     => '081234567892',
            'address'   => 'Jl. Gatot Subroto No. 5, Jakarta',
            'role'      => 'kurir',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Rina Wijaya',
            'email'     => 'pelanggan@kebab.com',
            'password'  => Hash::make('password'),
            'phone'     => '081234567893',
            'address'   => 'Jl. Thamrin No. 10, Jakarta',
            'role'      => 'pelanggan',
            'is_active' => true,
        ]);

        // ── User Tambahan ──────────────────────────────────────────
        $kasirData = [
            ['name' => 'Dian Permata',      'email' => 'kasir2@kebab.com',     'phone' => '081234567894'],
            ['name' => 'Fitri Handayani',   'email' => 'kasir3@kebab.com',     'phone' => '081234567895'],
        ];
        foreach ($kasirData as $data) {
            User::create(array_merge($data, [
                'password'  => Hash::make('password'),
                'address'   => 'Jakarta',
                'role'      => 'kasir',
                'is_active' => true,
            ]));
        }

        $kurirData = [
            ['name' => 'Agus Prasetyo',     'email' => 'kurir2@kebab.com',     'phone' => '081234567896'],
            ['name' => 'Doni Lesmana',      'email' => 'kurir3@kebab.com',     'phone' => '081234567897'],
            ['name' => 'Eko Supriyanto',    'email' => 'kurir4@kebab.com',     'phone' => '081234567898'],
        ];
        foreach ($kurirData as $data) {
            User::create(array_merge($data, [
                'password'  => Hash::make('password'),
                'address'   => 'Jakarta',
                'role'      => 'kurir',
                'is_active' => true,
            ]));
        }

        // Pelanggan tambahan (20 user)
        $pelangganNames = [
            'Andi Pratama', 'Bunga Citra', 'Citra Dewi', 'Deni Maulana', 'Eka Putri',
            'Fajar Nugroho', 'Gita Savitri', 'Hendra Kusuma', 'Indah Permata', 'Joko Susilo',
            'Kartika Sari', 'Lilis Suryani', 'Mega Wati', 'Nanda Pradana', 'Oki Setiawan',
            'Putri Ayu', 'Rizky Ramadhan', 'Sari Dewi', 'Teguh Prasetyo', 'Winda Anggraini',
        ];
        foreach ($pelangganNames as $i => $name) {
            User::create([
                'name'      => $name,
                'email'     => 'pelanggan' . ($i + 2) . '@kebab.com',
                'password'  => Hash::make('password'),
                'phone'     => '0812345679' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'address'   => 'Jakarta',
                'role'      => 'pelanggan',
                'is_active' => true,
            ]);
        }

        // Pemasok tambahan (5 user) — akan dilengkapi data supplier-nya di SupplierSeeder
        $pemasokNames = [
            'CV. Sumber Rezeki', 'UD. Berkah Abadi', 'PT. Pangan Makmur',
            'Toko Rempah Nusantara', 'CV. Segar Alami',
        ];
        foreach ($pemasokNames as $i => $name) {
            User::create([
                'name'      => $name,
                'email'     => 'pemasok' . ($i + 1) . '@kebab.com',
                'password'  => Hash::make('password'),
                'phone'     => '0812345680' . ($i + 1),
                'address'   => 'Jakarta',
                'role'      => 'pemasok',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ User berhasil dibuat');
    }
}
