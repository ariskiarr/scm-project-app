<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderDeliveryUpdate;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Purchase Order untuk pengadaan bahan baku kebab.
     */
    public function run(): void
    {
        $pemilik   = User::where('email', 'pemilik@kebab.com')->first();
        $suppliers = Supplier::all();
        $materials = RawMaterial::all();

        if (! $pemilik || $suppliers->isEmpty() || $materials->isEmpty()) return;

        // ── Draft PO ───────────────────────────────────────────────
        for ($i = 0; $i < 3; $i++) {
            $po = PurchaseOrder::create([
                'po_number'             => 'PO-' . now()->format('Ymd') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'supplier_id'           => $suppliers->random()->id,
                'created_by'            => $pemilik->id,
                'status'                => 'draft',
                'total_amount'          => 0,
                'expected_delivery_date' => Carbon::today()->addDays(rand(3, 7)),
                'notes'                 => 'Draft pesanan bahan baku',
            ]);
            $this->createItems($po, $materials, rand(2, 4));
            $po->recalculateTotal();
        }

        // ── Active POs (sent, confirmed, shipped) ──────────────────
        foreach (['sent', 'confirmed', 'shipped'] as $status) {
            for ($i = 0; $i < 2; $i++) {
                $po = PurchaseOrder::create([
                    'po_number'             => 'PO-' . now()->format('Ymd') . '-' . strtoupper(substr($status, 0, 3)) . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'supplier_id'           => $suppliers->random()->id,
                    'created_by'            => $pemilik->id,
                    'status'                => $status,
                    'total_amount'          => 0,
                    'expected_delivery_date' => Carbon::today()->addDays(rand(1, 5)),
                    'notes'                 => 'Pesanan bahan baku status: ' . $status,
                ]);
                $this->createItems($po, $materials, rand(2, 5));
                $po->recalculateTotal();

                // Buat delivery update
                $deliveryStatus = $status === 'shipped' ? 'shipped' : 'confirmed';
                PurchaseOrderDeliveryUpdate::create([
                    'purchase_order_id' => $po->id,
                    'updated_by'        => $pemilik->id,
                    'status'            => $deliveryStatus,
                    'description'       => 'PO status: ' . $deliveryStatus,
                ]);
            }
        }

        // ── Received PO ────────────────────────────────────────────
        for ($i = 0; $i < 5; $i++) {
            $supplier = $suppliers->random();
            $po = PurchaseOrder::create([
                'po_number'             => 'PO-' . now()->subDays(rand(3, 10))->format('Ymd') . '-RCV-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'supplier_id'           => $supplier->id,
                'created_by'            => $pemilik->id,
                'status'                => 'received',
                'total_amount'          => 0,
                'expected_delivery_date' => Carbon::today()->subDays(rand(1, 5)),
                'actual_delivery_date'  => Carbon::today()->subDays(rand(1, 3)),
                'notes'                 => 'Pesanan telah diterima',
            ]);
            $this->createItems($po, $materials, rand(2, 6), true);
            $po->recalculateTotal();

            PurchaseOrderDeliveryUpdate::create([
                'purchase_order_id' => $po->id,
                'updated_by'        => $pemilik->id,
                'status'            => 'received',
                'description'       => 'Barang telah diterima dan stok masuk gudang',
            ]);
        }

        $this->command->info('✅ Purchase Order berhasil dibuat');
    }

    private function createItems($po, $materials, int $count, bool $fullyReceived = false): void
    {
        $selected = $materials->random(min($count, $materials->count()));
        if ($selected instanceof RawMaterial) $selected = collect([$selected]);

        foreach ($selected as $material) {
            $qty = round(rand(5, 50) + (rand(0, 99) / 100), 2);
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'raw_material_id'   => $material->id,
                'quantity'          => $qty,
                'unit'              => $material->unit,
                'price_per_unit'    => $material->price_per_unit,
                'subtotal'          => round($qty * $material->price_per_unit, 2),
                'received_quantity' => $fullyReceived ? $qty : round(rand(0, (int)$qty) + (rand(0, 99) / 100), 2),
            ]);
        }
    }
}
