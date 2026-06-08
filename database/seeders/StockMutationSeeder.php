<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use App\Models\StockMutation;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockMutationSeeder extends Seeder
{
    /**
     * Mutasi stok untuk pergerakan bahan baku kebab.
     */
    public function run(): void
    {
        $pemilik    = User::where('email', 'pemilik@kebab.com')->first();
        $materials  = RawMaterial::all();
        $receivedPOs = PurchaseOrder::where('status', 'received')->get();

        if (! $pemilik) return;

        // ── Mutasi Masuk dari PO Received ──────────────────────────
        foreach ($receivedPOs as $po) {
            foreach ($po->items as $item) {
                $material = $item->rawMaterial;
                if (! $material) continue;

                $stockBefore = $material->current_stock;
                $stockAfter  = $stockBefore + $item->received_quantity;

                StockMutation::create([
                    'raw_material_id'   => $item->raw_material_id,
                    'created_by'        => $pemilik->id,
                    'purchase_order_id' => $po->id,
                    'type'              => 'in',
                    'quantity'          => $item->received_quantity,
                    'stock_before'      => $stockBefore,
                    'stock_after'       => $stockAfter,
                    'reference'         => 'PO#' . $po->po_number,
                    'notes'             => 'Penerimaan PO #' . $po->po_number,
                ]);

                // Update stok bahan baku
                $material->update(['current_stock' => $stockAfter]);
            }
        }

        // ── Mutasi Keluar (pemakaian produksi) ─────────────────────
        $usageRecords = [
            ['name' => 'Daging Sapi Iris',     'qty' => 12, 'unit' => 'kg'],
            ['name' => 'Daging Ayam Fillet',   'qty' => 8,  'unit' => 'kg'],
            ['name' => 'Tortilla/Kulit Kebab', 'qty' => 80, 'unit' => 'lembar'],
            ['name' => 'Selada',               'qty' => 4,  'unit' => 'kg'],
            ['name' => 'Tomat',                'qty' => 5,  'unit' => 'kg'],
            ['name' => 'Timun',                'qty' => 3,  'unit' => 'kg'],
            ['name' => 'Saus Tomat',           'qty' => 5,  'unit' => 'liter'],
            ['name' => 'Saus Sambal',          'qty' => 4,  'unit' => 'liter'],
            ['name' => 'Mayones',              'qty' => 3,  'unit' => 'liter'],
            ['name' => 'Keju Cheddar Slice',   'qty' => 30, 'unit' => 'lembar'],
            ['name' => 'Minyak Goreng',        'qty' => 6,  'unit' => 'liter'],
        ];

        foreach ($usageRecords as $record) {
            $material = $materials->firstWhere('name', $record['name']);
            if (! $material) continue;

            $stockBefore = $material->current_stock;
            $qty         = $record['qty'];
            $stockAfter  = max(0, $stockBefore - $qty);

            StockMutation::create([
                'raw_material_id' => $material->id,
                'created_by'      => $pemilik->id,
                'type'            => 'out',
                'quantity'        => $qty,
                'stock_before'    => $stockBefore,
                'stock_after'     => $stockAfter,
                'reference'       => 'Produksi Harian',
                'notes'           => 'Pemakaian produksi kebab harian - ' . now()->format('d/m/Y'),
            ]);

            // Update stok bahan baku
            $material->update(['current_stock' => $stockAfter]);
        }

        $this->command->info('✅ Mutasi stok berhasil dibuat');
    }
}
