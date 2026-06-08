<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupplierSeeder extends Seeder
{
    /**
     * Pemasok bahan baku untuk UMKM Kebab.
     */
    public function run(): void
    {
        // Ambil user pemasok yang sudah dibuat di UserSeeder
        $pemasokUsers = User::where('role', 'pemasok')->get();

        $suppliers = [
            [
                'company_name'        => 'Sumber Daging Sejahtera',
                'contact_person'      => 'Hendra Gunawan',
                'phone'               => '081298765432',
                'email'               => 'hendra@sumberdaging.com',
                'address'             => 'Jl. Raya Pasar Minggu No. 45, Jakarta Selatan',
                'bank_name'           => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name'   => 'Hendra Gunawan',
            ],
            [
                'company_name'        => 'Poultry Fresh Supply',
                'contact_person'      => 'Dewi Lestari',
                'phone'               => '085611223344',
                'email'               => 'dewi@poultryfresh.com',
                'address'             => 'Jl. Ciputat Raya No. 78, Tangerang Selatan',
                'bank_name'           => 'BRI',
                'bank_account_number' => '0987654321',
                'bank_account_name'   => 'Dewi Lestari',
            ],
            [
                'company_name'        => 'Tortilla Indah',
                'contact_person'      => 'Rudi Hartono',
                'phone'               => '087812345678',
                'email'               => 'rudi@tortillaindah.com',
                'address'             => 'Jl. Industri Raya Blok C No. 12, Bekasi',
                'bank_name'           => 'Mandiri',
                'bank_account_number' => '1122334455',
                'bank_account_name'   => 'Rudi Hartono',
            ],
            [
                'company_name'        => 'Sari Sayur Segar',
                'contact_person'      => 'Nurul Hidayah',
                'phone'               => '089912345678',
                'email'               => 'nurul@karangsayur.com',
                'address'             => 'Jl. Kebon Jeruk No. 33, Jakarta Barat',
                'bank_name'           => 'BNI',
                'bank_account_number' => '5566778899',
                'bank_account_name'   => 'Nurul Hidayah',
            ],
            [
                'company_name'        => 'Bumbu Khas Timur Tengah',
                'contact_person'      => 'Abdullah Malik',
                'phone'               => '081387654321',
                'email'               => 'abdullah@bumbuhalab.com',
                'address'             => 'Jl. Tanah Abang No. 21, Jakarta Pusat',
                'bank_name'           => 'BSI',
                'bank_account_number' => '9988776655',
                'bank_account_name'   => 'Abdullah Malik',
            ],
        ];

        foreach ($suppliers as $i => $data) {
            // Ambil user pemasok yang sesuai (jika ada), atau buat baru
            $user = $pemasokUsers[$i] ?? User::create([
                'name'      => $data['contact_person'],
                'email'     => $data['email'],
                'password'  => Hash::make('password'),
                'phone'     => $data['phone'],
                'address'   => $data['address'],
                'role'      => 'pemasok',
                'is_active' => true,
            ]);

            Supplier::create(array_merge($data, [
                'user_id'   => $user->id,
                'is_active' => true,
            ]));
        }
    }
}
