<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    /**
     * Bahan baku untuk produksi kebab UMKM.
     */
    public function run(): void
    {
        $materials = [
            ['code' => 'BHN-0001', 'name' => 'Daging Sapi Iris',         'unit' => 'kg',     'current_stock' => 50,  'minimum_stock' => 10,  'price_per_unit' => 85000],
            ['code' => 'BHN-0002', 'name' => 'Daging Ayam Fillet',       'unit' => 'kg',     'current_stock' => 40,  'minimum_stock' => 8,   'price_per_unit' => 32000],
            ['code' => 'BHN-0003', 'name' => 'Tortilla/Kulit Kebab',     'unit' => 'lembar', 'current_stock' => 500, 'minimum_stock' => 100, 'price_per_unit' => 2500],
            ['code' => 'BHN-0004', 'name' => 'Selada',                   'unit' => 'kg',     'current_stock' => 15,  'minimum_stock' => 5,   'price_per_unit' => 12000],
            ['code' => 'BHN-0005', 'name' => 'Tomat',                    'unit' => 'kg',     'current_stock' => 20,  'minimum_stock' => 5,   'price_per_unit' => 8000],
            ['code' => 'BHN-0006', 'name' => 'Timun',                    'unit' => 'kg',     'current_stock' => 18,  'minimum_stock' => 5,   'price_per_unit' => 6000],
            ['code' => 'BHN-0007', 'name' => 'Bawang Merah',             'unit' => 'kg',     'current_stock' => 12,  'minimum_stock' => 3,   'price_per_unit' => 25000],
            ['code' => 'BHN-0008', 'name' => 'Bawang Putih',             'unit' => 'kg',     'current_stock' => 8,   'minimum_stock' => 2,   'price_per_unit' => 28000],
            ['code' => 'BHN-0009', 'name' => 'Saus Tomat',               'unit' => 'liter',  'current_stock' => 25,  'minimum_stock' => 5,   'price_per_unit' => 15000],
            ['code' => 'BHN-0010', 'name' => 'Saus Sambal',              'unit' => 'liter',  'current_stock' => 25,  'minimum_stock' => 5,   'price_per_unit' => 15000],
            ['code' => 'BHN-0011', 'name' => 'Mayones',                  'unit' => 'liter',  'current_stock' => 20,  'minimum_stock' => 5,   'price_per_unit' => 22000],
            ['code' => 'BHN-0012', 'name' => 'Saus Thousand Island',     'unit' => 'liter',  'current_stock' => 15,  'minimum_stock' => 3,   'price_per_unit' => 25000],
            ['code' => 'BHN-0013', 'name' => 'Keju Cheddar Slice',       'unit' => 'lembar', 'current_stock' => 200, 'minimum_stock' => 50,  'price_per_unit' => 3500],
            ['code' => 'BHN-0014', 'name' => 'Minyak Goreng',            'unit' => 'liter',  'current_stock' => 30,  'minimum_stock' => 10,  'price_per_unit' => 14000],
            ['code' => 'BHN-0015', 'name' => 'Tepung Terigu',            'unit' => 'kg',     'current_stock' => 25,  'minimum_stock' => 5,   'price_per_unit' => 10000],
            ['code' => 'BHN-0016', 'name' => 'Bubuk Kari',               'unit' => 'gram',   'current_stock' => 500, 'minimum_stock' => 100, 'price_per_unit' => 500],
            ['code' => 'BHN-0017', 'name' => 'Jintan Bubuk',             'unit' => 'gram',   'current_stock' => 400, 'minimum_stock' => 100, 'price_per_unit' => 600],
            ['code' => 'BHN-0018', 'name' => 'Ketumbar Bubuk',           'unit' => 'gram',   'current_stock' => 350, 'minimum_stock' => 100, 'price_per_unit' => 400],
            ['code' => 'BHN-0019', 'name' => 'Kecap Manis',              'unit' => 'liter',  'current_stock' => 15,  'minimum_stock' => 3,   'price_per_unit' => 18000],
            ['code' => 'BHN-0020', 'name' => 'Cabai',                    'unit' => 'kg',     'current_stock' => 10,  'minimum_stock' => 2,   'price_per_unit' => 35000],
        ];

        foreach ($materials as $data) {
            RawMaterial::create($data);
        }
    }
}
