<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Product;
use App\Models\OrderDeliveryUpdate;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PelangganController extends Controller
{
    public function dashboard()
    {
        $products = Product::where('is_available', true)->where('stock', '>', 0)->get();
        
        $activeOrders = CustomerOrder::with(['latestDeliveryUpdate'])
            ->where('customer_id', auth()->id())
            ->whereNotIn('status', [CustomerOrder::STATUS_DELIVERED, CustomerOrder::STATUS_CANCELLED])
            ->latest()
            ->get();

        return view('pelanggan.dashboard', compact('products', 'activeOrders'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $checkoutItems = [];
        $subtotal = 0;
        
        foreach ($request->items as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            $qty = $itemData['quantity'];
            
            if ($product->stock < $qty) {
                return redirect()->route('pelanggan.dashboard')->withErrors([
                    'error' => "Stok produk {$product->name} tidak mencukupi."
                ]);
            }

            $itemSubtotal = $product->price * $qty;
            $subtotal += $itemSubtotal;

            $checkoutItems[] = [
                'product' => $product,
                'quantity' => $qty,
                'subtotal' => $itemSubtotal
            ];
        }

        $shippingCost = 10000; // Flat shipping rate
        $totalAmount = $subtotal + $shippingCost;

        return view('pelanggan.checkout', compact('checkoutItems', 'subtotal', 'shippingCost', 'totalAmount'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', Rule::in(['transfer', 'qris', 'cod'])],
            'shipping_address' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($request) {
            $order_number = 'KB-' . now()->format('YmdHis') . '-' . rand(10, 99);
            $shippingCost = 10000;

            $order = CustomerOrder::create([
                'order_number' => $order_number,
                'customer_id' => auth()->id(),
                'order_type' => 'online',
                'status' => CustomerOrder::STATUS_PENDING,
                'payment_method' => $request->payment_method,
                'payment_status' => CustomerOrder::PAYMENT_STATUS_UNPAID,
                'subtotal' => 0,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'total_amount' => $shippingCost,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
            ]);

            $subtotal = 0;
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                if ($product->stock < $itemData['quantity']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

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
                'total_amount' => $subtotal + $shippingCost,
            ]);

            // Add first delivery update log
            OrderDeliveryUpdate::create([
                'customer_order_id' => $order->id,
                'updated_by' => auth()->id(),
                'status' => CustomerOrder::STATUS_PENDING,
                'location' => 'Sistem Kebab Berkah',
                'description' => 'Pesanan berhasil dibuat oleh pelanggan. Menunggu konfirmasi pembayaran dan dapur.',
            ]);

            // Notify Cashiers
            $cashiers = User::where('role', 'kasir')->get();
            foreach ($cashiers as $kasir) {
                Notification::send(
                    $kasir->id,
                    Notification::TYPE_NEW_ORDER,
                    'Pesanan Online Baru!',
                    "Ada pesanan baru {$order->order_number} dari {$order->customer->name} senilai Rp " . number_format($order->total_amount, 0, ',', '.'),
                    $order
                );
            }

            return $order;
        });

        return redirect()->route('pelanggan.tracking', $order->id)->with('success', 'Pesanan Anda berhasil ditempatkan!');
    }

    public function tracking($id)
    {
        $order = CustomerOrder::with(['items', 'deliveryUpdates.updatedBy', 'kurir'])->findOrFail($id);
        
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $deliveryUpdates = $order->deliveryUpdates()->latest()->get();

        return view('pelanggan.tracking', compact('order', 'deliveryUpdates'));
    }

    public function riwayat()
    {
        $orders = CustomerOrder::with(['items', 'latestDeliveryUpdate'])
            ->where('customer_id', auth()->id())
            ->latest()
            ->get();

        return view('pelanggan.riwayat', compact('orders'));
    }
}
