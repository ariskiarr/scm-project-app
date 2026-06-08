<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\SupplierRawMaterial;
use Illuminate\Database\Seeder;

class SupplierRawMaterialSeeder extends Seeder
{
    /**
     * Relasi pemasok dengan bahan baku yang mereka sediakan.
     * Disesuaikan dengan konteks UMKM Kebab.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $materials = RawMaterial::all();

        // Mapping supplier → bahan baku yang relevan
        $supplierMaterials = [
            'Sumber Daging Sejahtera'    => ['Daging Sapi Iris', 'Daging Ayam Fillet'],
            'Poultry Fresh Supply'       => ['Daging Ayam Fillet'],
            'Tortilla Indah'             => ['Tortilla/Kulit Kebab', 'Tepung Terigu'],
            'Sari Sayur Segar'           => ['Selada', 'Tomat', 'Timun', 'Bawang Merah', 'Bawang Putih', 'Cabai'],
            'Bumbu Khas Timur Tengah'    => ['Bubuk Kari', 'Jintan Bubuk', 'Ketumbar Bubuk', 'Kecap Manis', 'Saus Tomat', 'Saus Sambal', 'Mayones', 'Saus Thousand Island'],
        ];

        foreach ($suppliers as $supplier) {
            $materialNames = $supplierMaterials[$supplier->company_name] ?? [];

            foreach ($materialNames as $name) {
                $material = $materials->firstWhere('name', $name);
                if (! $material) continue;

                SupplierRawMaterial::create([
                    'supplier_id'      => $supplier->id,
                    'raw_material_id'  => $material->id,
                    'price_per_unit'   => $material->price_per_unit * fake()->randomFloat(2, 1.1, 1.4),
                    'minimum_order_qty' => fake()->randomFloat(2, 1, 5),
                    'available_stock'  => $material->current_stock * 2,
                    'lead_time_days'   => fake()->numberBetween(1, 4),
                    'is_active'        => true,
                ]);
            }
        }
    }
}
