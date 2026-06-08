<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderDeliveryUpdate;
use App\Models\DailySalesSummary;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KasirController extends Controller
{
    public function dashboard()
    {
        // Totals for today
        $todayOrders = CustomerOrder::today()->where('status', '!=', CustomerOrder::STATUS_CANCELLED)->get();
        $todayRevenue = $todayOrders->where('payment_status', CustomerOrder::PAYMENT_STATUS_PAID)->sum('total_amount');
        $pendingCount = CustomerOrder::pending()->count();

        $activeOrders = CustomerOrder::with(['customer', 'kurir'])
            ->whereIn('status', [CustomerOrder::STATUS_PENDING, CustomerOrder::STATUS_CONFIRMED, CustomerOrder::STATUS_PROCESSING])
            ->latest()
            ->take(10)
            ->get();

        return view('kasir.dashboard', compact('todayRevenue', 'todayOrders', 'pendingCount', 'activeOrders'));
    }

    // --- POS TRANSACTION INPUT ---
    public function transaksiCreate()
    {
        $products = Product::where('is_available', true)->where('stock', '>', 0)->get();
        return view('kasir.transaksi', compact('products'));
    }

    public function transaksiStore(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'qris', 'transfer'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($request) {
            $order_number = 'KB-' . now()->format('YmdHis') . '-' . rand(10, 99);

            // Create walk-in customer user if not specified, or just link to a dummy/walk-in ID
            // We can just use the kasir user as customer, or find the default customer: pelanggan@kebab.com
            $defaultCustomer = User::where('role', 'pelanggan')->first();
            $customerId = $defaultCustomer ? $defaultCustomer->id : auth()->id();

            $order = CustomerOrder::create([
                'order_number' => $order_number,
                'customer_id' => $customerId,
                'kasir_id' => auth()->id(),
                'order_type' => 'offline',
                'status' => CustomerOrder::STATUS_DELIVERED, // Offline is instantly delivered
                'payment_method' => $request->payment_method,
                'payment_status' => CustomerOrder::PAYMENT_STATUS_PAID,
                'subtotal' => 0,
                'shipping_cost' => 0,
                'discount' => $request->discount ?? 0,
                'total_amount' => 0,
                'paid_at' => now(),
            ]);

            $subtotal = 0;
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                // Decrement stock
                if ($product->stock < $itemData['quantity']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi. Tersedia: {$product->stock}");
                }
                $product->decrement('stock', $itemData['quantity']);

                $itemSubtotal = $product->price * $itemData['quantity'];
                $subtotal += $itemSubtotal;

                CustomerOrderItem::create([
                    'customer_order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => max(0, $subtotal - ($request->discount ?? 0)),
            ]);

            return $order;
        });

        // Automatically regenerate summary
        DailySalesSummary::generateForDate(today(), auth()->id());

        return redirect()->route('kasir.dashboard')->with('success', 'Transaksi kasir berhasil dicatat. Nomor: ' . $order->order_number);
    }

    // --- MANAGE CUSTOMER ORDERS ---
    public function pesanan()
    {
        $orders = CustomerOrder::with(['customer', 'kurir', 'items'])
            ->where('order_type', 'online')
            ->latest()
            ->get();

        $couriers = User::where('role', 'kurir')->where('is_active', true)->get();

        return view('kasir.pesanan', compact('orders', 'couriers'));
    }

    public function pesananUpdateStatus(Request $request, $id)
    {
        $order = CustomerOrder::findOrFail($id);
        $request->validate([
            'status' => ['required', Rule::in([
                CustomerOrder::STATUS_CONFIRMED,
                CustomerOrder::STATUS_PROCESSING,
                CustomerOrder::STATUS_READY_TO_SHIP,
                CustomerOrder::STATUS_CANCELLED
            ])],
            'kurir_id' => ['required_if:status,ready_to_ship', 'nullable', 'exists:users,id'],
        ]);

        DB::transaction(function () use ($request, $order) {
            $oldStatus = $order->status;
            $newStatus = $request->status;

            $updateData = [
                'status' => $newStatus,
                'kasir_id' => auth()->id(),
            ];

            if ($newStatus === CustomerOrder::STATUS_READY_TO_SHIP) {
                $updateData['kurir_id'] = $request->kurir_id;
            }

            if ($newStatus === CustomerOrder::STATUS_CONFIRMED && $order->payment_method === 'cash') {
                $updateData['payment_status'] = CustomerOrder::PAYMENT_STATUS_PAID;
                $updateData['paid_at'] = now();
            }

            $order->update($updateData);

            // Deduct stock when order is first confirmed/processed (if not deducted yet)
            if (in_array($newStatus, [CustomerOrder::STATUS_CONFIRMED, CustomerOrder::STATUS_PROCESSING]) &&
                in_array($oldStatus, [CustomerOrder::STATUS_PENDING])) {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('stock', $item->quantity);
                    }
                }
            }

            // Create Delivery Update log
            $statusLabel = CustomerOrder::statusLabels()[$newStatus] ?? $newStatus;

            OrderDeliveryUpdate::create([
                'customer_order_id' => $order->id,
                'updated_by' => auth()->id(),
                'status' => $newStatus === CustomerOrder::STATUS_READY_TO_SHIP ? OrderDeliveryUpdate::STATUS_READY_TO_SHIP : $newStatus,
                'location' => 'Dapur Kebab Time',
                'description' => "Pesanan berstatus {$statusLabel}.",
            ]);

            // Notify Customer
            if ($order->customer_id) {
                $notifType = Notification::TYPE_GENERAL;
                if ($newStatus === CustomerOrder::STATUS_CONFIRMED) {
                    $notifType = Notification::TYPE_ORDER_CONFIRMED;
                } elseif ($newStatus === CustomerOrder::STATUS_READY_TO_SHIP) {
                    $notifType = Notification::TYPE_ORDER_SHIPPED;
                }

                Notification::send(
                    $order->customer_id,
                    $notifType,
                    'Update Pesanan: ' . $statusLabel,
                    "Pesanan Anda #{$order->order_number} saat ini: {$statusLabel}.",
                    $order
                );
            }

            // Notify Courier if ready to ship
            if ($newStatus === CustomerOrder::STATUS_READY_TO_SHIP && $request->kurir_id) {
                Notification::send(
                    $request->kurir_id,
                    Notification::TYPE_GENERAL,
                    'Pesanan Siap Kirim!',
                    "Anda ditugaskan mengirim pesanan #{$order->order_number} ke alamat pelanggan.",
                    $order
                );
            }
        });

        // Automatically regenerate summary
        DailySalesSummary::generateForDate(today(), auth()->id());

        return redirect()->route('kasir.pesanan')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    // --- DAILY TRANSACTION SUMMARY ---
    public function rekap()
    {
        $summaries = DailySalesSummary::orderBy('summary_date', 'desc')->get();

        // Calculate current un-summarized numbers for today
        $todaySummary = DailySalesSummary::where('summary_date', today())->first();

        return view('kasir.rekap', compact('summaries', 'todaySummary'));
    }

    public function rekapGenerate()
    {
        DailySalesSummary::generateForDate(today(), auth()->id());
        return redirect()->route('kasir.rekap')->with('success', 'Rekap transaksi hari ini berhasil dibuat/diperbarui.');
    }
}
