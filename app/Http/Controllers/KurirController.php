<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\OrderDeliveryUpdate;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KurirController extends Controller
{
    public function dashboard()
    {
        $orders = CustomerOrder::with(['customer', 'items', 'latestDeliveryUpdate'])
            ->where('kurir_id', auth()->id())
            ->whereIn('status', [
                CustomerOrder::STATUS_READY_TO_SHIP,
                CustomerOrder::STATUS_SHIPPED,
            ])
            ->latest()
            ->get();

        return view('kurir.dashboard', compact('orders'));
    }

    public function updatePengiriman(Request $request, $id)
    {
        $order = CustomerOrder::where('kurir_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'status' => ['required', Rule::in([
                OrderDeliveryUpdate::STATUS_PICKED_UP,
                OrderDeliveryUpdate::STATUS_IN_TRANSIT,
                OrderDeliveryUpdate::STATUS_DELIVERED,
                OrderDeliveryUpdate::STATUS_FAILED_DELIVERY,
            ])],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $order) {
            $status = $request->status;
            
            // Map delivery status to order status
            $orderStatus = $order->status;
            if ($status === OrderDeliveryUpdate::STATUS_PICKED_UP || $status === OrderDeliveryUpdate::STATUS_IN_TRANSIT) {
                $orderStatus = CustomerOrder::STATUS_SHIPPED;
            } elseif ($status === OrderDeliveryUpdate::STATUS_DELIVERED) {
                $orderStatus = CustomerOrder::STATUS_DELIVERED;
            }

            $orderData = ['status' => $orderStatus];

            // If COD and delivered, mark as paid
            if ($status === OrderDeliveryUpdate::STATUS_DELIVERED) {
                if ($order->payment_method === 'cod') {
                    $orderData['payment_status'] = CustomerOrder::PAYMENT_STATUS_PAID;
                    $orderData['paid_at'] = now();
                }
            }

            $order->update($orderData);

            // Create Delivery Update Log
            OrderDeliveryUpdate::create([
                'customer_order_id' => $order->id,
                'updated_by' => auth()->id(),
                'status' => $status,
                'location' => $request->location,
                'description' => $request->description,
            ]);

            // Notify Customer
            if ($order->customer_id) {
                $statusLabel = OrderDeliveryUpdate::statusLabels()[$status] ?? $status;
                Notification::send(
                    $order->customer_id,
                    Notification::TYPE_ORDER_DELIVERED,
                    'Pengiriman Kebab Anda: ' . $statusLabel,
                    "Kebab Anda #{$order->order_number}: {$statusLabel}. Lokasi: {$request->location}. Keterangan: {$request->description}",
                    $order
                );
            }
        });

        return redirect()->route('kurir.dashboard')->with('success', 'Status pengiriman pesanan berhasil diperbarui.');
    }
}
