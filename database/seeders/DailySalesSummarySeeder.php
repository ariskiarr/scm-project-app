<?php

namespace Database\Seeders;

use App\Models\CustomerOrder;
use App\Models\DailySalesSummary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailySalesSummarySeeder extends Seeder
{
    /**
     * Rekap penjualan harian untuk UMKM Kebab (30 hari terakhir).
     */
    public function run(): void
    {
        $pemilik = User::where('email', 'pemilik@kebab.com')->first();
        if (! $pemilik) return;

        // Rekap hari ini berdasarkan data pesanan delivered
        DailySalesSummary::generateForDate(today(), $pemilik->id);

        // Buat rekap untuk 30 hari kebelakang dengan data simulasi
        for ($i = 1; $i <= 30; $i++) {
            $date = Carbon::today()->subDays($i);

            $deliveredOrders = CustomerOrder::where('status', 'delivered')
                ->whereDate('created_at', $date)
                ->get();

            if ($deliveredOrders->isNotEmpty()) {
                DailySalesSummary::generateForDate($date, $pemilik->id);
            } else {
                // Simulasi data harian untuk hari-hari tanpa pesanan
                $totalTransaction = rand(8, 30);
                $totalOnline      = rand(3, $totalTransaction);
                $totalOffline     = $totalTransaction - $totalOnline;
                $avgOrderValue    = rand(18000, 30000);
                $grossSales       = $totalTransaction * $avgOrderValue;
                $discount         = rand(0, 2) > 0 ? $totalTransaction * rand(1000, 3000) : 0;
                $shippingCost     = $totalOnline * 5000;
                $netSales         = $grossSales - $discount + $shippingCost;
                $cancelled        = rand(0, 3);

                DailySalesSummary::create([
                    'summary_date'         => $date->toDateString(),
                    'total_transactions'   => $totalTransaction,
                    'total_orders_online'  => $totalOnline,
                    'total_orders_offline' => $totalOffline,
                    'gross_sales'          => $grossSales,
                    'total_discount'       => $discount,
                    'total_shipping_cost'  => $shippingCost,
                    'net_sales'            => $netSales,
                    'cancelled_orders'     => $cancelled,
                    'generated_by'         => $pemilik->id,
                ]);
            }
        }

        $this->command->info('✅ Rekap penjualan harian berhasil dibuat');
    }
}
