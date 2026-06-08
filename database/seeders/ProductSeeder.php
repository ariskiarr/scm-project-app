<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Produk-produk yang dijual UMKM Kebab.
     */
    public function run(): void
    {
        $products = [
            // Kebab
            ['code' => 'PRD-0001', 'name' => 'Kebab Sapi Spesial',     'category' => 'Kebab',   'unit' => 'pcs',  'price' => 25000, 'stock' => 50, 'description' => 'Kebab sapi dengan daging pilihan, sayuran segar, dan saus khas.'],
            ['code' => 'PRD-0002', 'name' => 'Kebab Ayam Pedas',       'category' => 'Kebab',   'unit' => 'pcs',  'price' => 22000, 'stock' => 50, 'description' => 'Kebab ayam fillet dengan bumbu pedas menggugah selera.'],
            ['code' => 'PRD-0003', 'name' => 'Kebab Sapi Keju',        'category' => 'Kebab',   'unit' => 'pcs',  'price' => 28000, 'stock' => 40, 'description' => 'Kebab sapi dengan tambahan keju cheddar leleh.'],
            ['code' => 'PRD-0004', 'name' => 'Kebab Ayam BBQ',         'category' => 'Kebab',   'unit' => 'pcs',  'price' => 24000, 'stock' => 45, 'description' => 'Kebab ayam dengan saus BBQ istimewa.'],
            ['code' => 'PRD-0005', 'name' => 'Kebab Jumbo Sapi',       'category' => 'Kebab',   'unit' => 'pcs',  'price' => 35000, 'stock' => 30, 'description' => 'Kebab jumbo ukuran besar dengan daging sapi ganda.'],
            ['code' => 'PRD-0006', 'name' => 'Kebab Mini',             'category' => 'Kebab',   'unit' => 'pcs',  'price' => 12000, 'stock' => 100, 'description' => 'Kebab mini ukuran kecil cocok untuk camilan.'],
            ['code' => 'PRD-0007', 'name' => 'Kebab Telur',            'category' => 'Kebab',   'unit' => 'pcs',  'price' => 15000, 'stock' => 60, 'description' => 'Kebab isi telur dadar dengan sayuran segar.'],
            ['code' => 'PRD-0008', 'name' => 'Kebab Sayur',            'category' => 'Kebab',   'unit' => 'pcs',  'price' => 13000, 'stock' => 70, 'description' => 'Kebab vegetarian dengan aneka sayuran segar.'],
            // Minuman
            ['code' => 'PRD-0009', 'name' => 'Es Teh Manis',           'category' => 'Minuman', 'unit' => 'gelas', 'price' => 5000,  'stock' => 100, 'description' => 'Es teh manis segar pelepas dahaga.'],
            ['code' => 'PRD-0010', 'name' => 'Es Jeruk',               'category' => 'Minuman', 'unit' => 'gelas', 'price' => 7000,  'stock' => 80,  'description' => 'Es jeruk peras segar.'],
            ['code' => 'PRD-0011', 'name' => 'Air Mineral',            'category' => 'Minuman', 'unit' => 'botol', 'price' => 3000,  'stock' => 120, 'description' => 'Air mineral kemasan botol.'],
            ['code' => 'PRD-0012', 'name' => 'Lemon Tea',              'category' => 'Minuman', 'unit' => 'gelas', 'price' => 8000,  'stock' => 75,  'description' => 'Teh lemon segar dengan perasan jeruk lemon asli.'],
            ['code' => 'PRD-0013', 'name' => 'Milkshake Coklat',       'category' => 'Minuman', 'unit' => 'gelas', 'price' => 15000, 'stock' => 40,  'description' => 'Milkshake coklat creamy dan nikmat.'],
            // Makanan Pendamping
            ['code' => 'PRD-0014', 'name' => 'Kentang Goreng',         'category' => 'Snack',   'unit' => 'porsi', 'price' => 10000, 'stock' => 60,  'description' => 'Kentang goreng renyah dengan saus sambal & mayones.'],
            ['code' => 'PRD-0015', 'name' => 'Nugget Ayam',            'category' => 'Snack',   'unit' => 'pcs',   'price' => 8000,  'stock' => 80,  'description' => 'Nugget ayam crispy pendamping kebab.'],
            ['code' => 'PRD-0016', 'name' => 'Onion Ring',             'category' => 'Snack',   'unit' => 'porsi', 'price' => 12000, 'stock' => 50,  'description' => 'Cincin bawang goreng renyah.'],
            ['code' => 'PRD-0017', 'name' => 'Mie Goreng',             'category' => 'Makanan', 'unit' => 'pcs',   'price' => 12000, 'stock' => 60,  'description' => 'Mie goreng instan dengan telur dan sayuran.'],
            ['code' => 'PRD-0018', 'name' => 'Mie Kuah',               'category' => 'Makanan', 'unit' => 'pcs',   'price' => 12000, 'stock' => 60,  'description' => 'Mie kuah hangat dengan topping telur.'],
        ];

        foreach ($products as $data) {
            Product::create(array_merge($data, [
                'is_available' => true,
                'image'        => null,
            ]));
        }
    }
}
