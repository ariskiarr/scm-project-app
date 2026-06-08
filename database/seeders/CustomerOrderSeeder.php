<?php

namespace Database\Seeders;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\OrderDeliveryUpdate;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CustomerOrderSeeder extends Seeder
{
    /**
     * Pesanan pelanggan untuk produk kebab.
     */
    public function run(): void
    {
        $kasirList     = User::where('role', 'kasir')->get();
        $kurirList     = User::where('role', 'kurir')->get();
        $pelanggan     = User::where('email', 'pelanggan@kebab.com')->first();
        $pelangganList = User::where('role', 'pelanggan')->get();
        $products      = Product::all();

        if ($products->isEmpty()) return;

        $orderNumber = 1;

        // ── Pending Orders ─────────────────────────────────────────
        for ($i = 0; $i < 5; $i++) {
            $order = CustomerOrder::create([
                'order_number'  => 'ORD-' . now()->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                'customer_id'   => $pelangganList->random()->id,
                'order_type'    => rand(0, 1) ? 'online' : 'offline',
                'status'        => 'pending',
                'payment_method' => collect(['cash', 'transfer', 'qris', 'cod'])->random(),
                'payment_status' => 'unpaid',
                'subtotal'      => 0,
                'shipping_cost' => rand(0, 1) ? 5000 : 0,
                'discount'      => 0,
                'total_amount'  => 0,
                'shipping_address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Jakarta',
                'notes'         => 'Pesanan baru menunggu konfirmasi',
            ]);
            $this->createItems($order, $products, rand(1, 3));
            $order->recalculateTotal();
        }

        // ── Confirmed & Processing ─────────────────────────────────
        foreach (['confirmed', 'processing'] as $status) {
            for ($i = 0; $i < 4; $i++) {
                $order = CustomerOrder::create([
                    'order_number'  => 'ORD-' . now()->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                    'customer_id'   => $pelangganList->random()->id,
                    'kasir_id'      => $kasirList->random()->id,
                    'order_type'    => rand(0, 1) ? 'online' : 'offline',
                    'status'        => $status,
                    'payment_method' => collect(['cash', 'transfer', 'qris', 'cod'])->random(),
                    'payment_status' => 'paid',
                    'subtotal'      => 0,
                    'shipping_cost' => rand(0, 1) ? 5000 : 0,
                    'discount'      => rand(0, 1) ? rand(2000, 5000) : 0,
                    'total_amount'  => 0,
                    'shipping_address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Jakarta',
                    'notes'         => 'Pesanan status: ' . $status,
                    'paid_at'       => now(),
                ]);
                $this->createItems($order, $products, rand(1, 4));
                $order->recalculateTotal();
            }
        }

        // ── Ready To Ship ──────────────────────────────────────────
        for ($i = 0; $i < 4; $i++) {
            $kurir = $kurirList->random();
            $order = CustomerOrder::create([
                'order_number'  => 'ORD-' . now()->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                'customer_id'   => $pelangganList->random()->id,
                'kasir_id'      => $kasirList->random()->id,
                'kurir_id'      => $kurir->id,
                'order_type'    => 'online',
                'status'        => 'ready_to_ship',
                'payment_method' => collect(['transfer', 'qris', 'cod'])->random(),
                'payment_status' => 'paid',
                'subtotal'      => 0,
                'shipping_cost' => 5000,
                'discount'      => rand(0, 1) ? rand(2000, 5000) : 0,
                'total_amount'  => 0,
                'shipping_address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Jakarta',
                'notes'         => 'Pesanan siap dikirim',
                'paid_at'       => now(),
            ]);
            $this->createItems($order, $products, rand(1, 3));
            $order->recalculateTotal();

            OrderDeliveryUpdate::create([
                'customer_order_id' => $order->id,
                'updated_by'        => $kurir->id,
                'status'            => 'ready_to_ship',
                'location'          => 'Gerai Kebab Berkah',
                'description'       => 'Pesanan siap untuk diambil kurir',
            ]);
        }

        // ── Shipped (In Transit) ───────────────────────────────────
        for ($i = 0; $i < 5; $i++) {
            $kurir = $kurirList->random();
            $order = CustomerOrder::create([
                'order_number'  => 'ORD-' . now()->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                'customer_id'   => $pelangganList->random()->id,
                'kasir_id'      => $kasirList->random()->id,
                'kurir_id'      => $kurir->id,
                'order_type'    => 'online',
                'status'        => 'shipped',
                'payment_method' => collect(['transfer', 'qris', 'cod'])->random(),
                'payment_status' => 'paid',
                'subtotal'      => 0,
                'shipping_cost' => 5000,
                'discount'      => rand(0, 1) ? rand(2000, 5000) : 0,
                'total_amount'  => 0,
                'shipping_address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Jakarta',
                'notes'         => 'Pesanan dalam perjalanan',
                'paid_at'       => now(),
            ]);
            $this->createItems($order, $products, rand(1, 3));
            $order->recalculateTotal();

            OrderDeliveryUpdate::create([
                'customer_order_id' => $order->id,
                'updated_by'        => $kurir->id,
                'status'            => 'in_transit',
                'location'          => 'Dalam perjalanan menuju pelanggan',
                'description'       => 'Kurir sedang dalam perjalanan mengantar pesanan',
            ]);
        }

        // ── Delivered ──────────────────────────────────────────────
        for ($i = 0; $i < 20; $i++) {
            $kurir = $kurirList->random();
            $createdAt = Carbon::today()->subDays(rand(0, 14))->addHours(rand(8, 20))->addMinutes(rand(0, 59));
            $order = CustomerOrder::create([
                'order_number'  => 'ORD-' . $createdAt->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                'customer_id'   => $pelangganList->random()->id,
                'kasir_id'      => $kasirList->random()->id,
                'kurir_id'      => $kurir->id,
                'order_type'    => rand(0, 1) ? 'online' : 'offline',
                'status'        => 'delivered',
                'payment_method' => collect(['cash', 'transfer', 'qris', 'cod'])->random(),
                'payment_status' => 'paid',
                'subtotal'      => 0,
                'shipping_cost' => rand(0, 1) ? 5000 : 0,
                'discount'      => rand(0, 1) ? rand(2000, 5000) : 0,
                'total_amount'  => 0,
                'shipping_address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Jakarta',
                'notes'         => 'Pesanan telah selesai',
                'paid_at'       => $createdAt->copy()->addMinutes(rand(1, 30)),
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt->copy()->addHours(2),
            ]);
            $this->createItems($order, $products, rand(1, 5));
            $order->recalculateTotal();

            OrderDeliveryUpdate::create([
                'customer_order_id' => $order->id,
                'updated_by'        => $kurir->id,
                'status'            => 'delivered',
                'location'          => 'Alamat pelanggan',
                'description'       => 'Pesanan telah diterima oleh pelanggan',
            ]);
        }

        // ── Cancelled ──────────────────────────────────────────────
        for ($i = 0; $i < 5; $i++) {
            $order = CustomerOrder::create([
                'order_number'  => 'ORD-' . now()->subDays(rand(1, 5))->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                'customer_id'   => $pelangganList->random()->id,
                'order_type'    => rand(0, 1) ? 'online' : 'offline',
                'status'        => 'cancelled',
                'payment_method' => collect(['cash', 'transfer', 'qris', 'cod'])->random(),
                'payment_status' => 'refunded',
                'subtotal'      => 0,
                'shipping_cost' => rand(0, 1) ? 5000 : 0,
                'discount'      => 0,
                'total_amount'  => 0,
                'shipping_address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Jakarta',
                'notes'         => 'Pesanan dibatalkan oleh pelanggan',
            ]);
            $this->createItems($order, $products, rand(1, 2));
            $order->recalculateTotal();
        }

        // ── Past orders untuk pelanggan demo (Rina) ────────────────
        if ($pelanggan) {
            for ($i = 0; $i < 3; $i++) {
                $kurir = $kurirList->random();
                $createdAt = Carbon::today()->subDays(rand(1, 10))->addHours(rand(8, 20))->addMinutes(rand(0, 59));
                $order = CustomerOrder::create([
                    'order_number'  => 'ORD-' . $createdAt->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                    'customer_id'   => $pelanggan->id,
                    'kasir_id'      => $kasirList->random()->id,
                    'kurir_id'      => $kurir->id,
                    'order_type'    => 'online',
                    'status'        => 'delivered',
                    'payment_method' => 'transfer',
                    'payment_status' => 'paid',
                    'subtotal'      => 0,
                    'shipping_cost' => 5000,
                    'discount'      => 0,
                    'total_amount'  => 0,
                    'shipping_address' => 'Jl. Thamrin No. 10, Jakarta',
                    'notes'         => 'Pesanan Rina',
                    'paid_at'       => $createdAt->copy()->addMinutes(rand(1, 15)),
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt->copy()->addHours(2),
                ]);
                $this->createItems($order, $products, rand(1, 3));
                $order->recalculateTotal();

                OrderDeliveryUpdate::create([
                    'customer_order_id' => $order->id,
                    'updated_by'        => $kurir->id,
                    'status'            => 'delivered',
                    'location'          => 'Jl. Thamrin No. 10, Jakarta',
                    'description'       => 'Pesanan telah diterima oleh Rina',
                ]);
            }

            // Order aktif untuk Rina
            $order = CustomerOrder::create([
                'order_number'  => 'ORD-' . now()->format('Ymd') . '-' . str_pad($orderNumber++, 4, '0', STR_PAD_LEFT),
                'customer_id'   => $pelanggan->id,
                'kasir_id'      => $kasirList->random()->id,
                'order_type'    => 'online',
                'status'        => 'processing',
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'subtotal'      => 0,
                'shipping_cost' => 5000,
                'discount'      => 0,
                'total_amount'  => 0,
                'shipping_address' => 'Jl. Thamrin No. 10, Jakarta',
                'notes'         => 'Tolong potong kebab jadi 2 bagian',
                'paid_at'       => now(),
            ]);
            $this->createItems($order, $products, 2);
            $order->recalculateTotal();
        }

        $this->command->info('✅ Pesanan pelanggan berhasil dibuat');
    }

    private function createItems($order, $products, int $count): void
    {
        $selected = $products->random(min($count, $products->count()));
        if ($selected instanceof Product) $selected = collect([$selected]);

        foreach ($selected as $product) {
            $qty = rand(1, 5);
            CustomerOrderItem::create([
                'customer_order_id' => $order->id,
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'quantity'          => $qty,
                'price'             => $product->price,
                'subtotal'          => $qty * $product->price,
            ]);
        }
    }
}
