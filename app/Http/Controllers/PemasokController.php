<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDeliveryUpdate;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PemasokController extends Controller
{
    protected function getSupplier()
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier) {
            abort(403, 'Profil Pemasok Anda belum dikonfigurasi oleh pemilik.');
        }
        return $supplier;
    }

    public function dashboard()
    {
        $supplier = $this->getSupplier();
        $purchaseOrders = PurchaseOrder::with(['items.rawMaterial', 'latestDeliveryUpdate', 'creator'])
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->get();
            
        return view('pemasok.dashboard', compact('purchaseOrders', 'supplier'));
    }

    public function confirmPO(Request $request, $id)
    {
        $supplier = $this->getSupplier();
        $po = PurchaseOrder::where('supplier_id', $supplier->id)->findOrFail($id);
        
        if ($po->status !== PurchaseOrder::STATUS_SENT) {
            return back()->withErrors(['error' => 'Purchase Order tidak sedang dalam status menunggu konfirmasi.']);
        }

        DB::transaction(function () use ($po) {
            $po->update(['status' => PurchaseOrder::STATUS_CONFIRMED]);

            PurchaseOrderDeliveryUpdate::create([
                'purchase_order_id' => $po->id,
                'updated_by' => auth()->id(),
                'status' => PurchaseOrderDeliveryUpdate::STATUS_CONFIRMED,
                'description' => 'Pesanan dikonfirmasi oleh pemasok dan sedang disiapkan.',
            ]);

            // Notify Owner
            $owner = User::where('role', 'pemilik')->first();
            if ($owner) {
                Notification::send(
                    $owner->id,
                    Notification::TYPE_PO_CONFIRMED,
                    'PO Dikonfirmasi: ' . $po->po_number,
                    "Pemasok {$po->supplier->company_name} telah mengonfirmasi Purchase Order {$po->po_number}.",
                    $po
                );
            }
        });

        return redirect()->route('pemasok.dashboard')->with('success', 'Purchase Order berhasil dikonfirmasi.');
    }

    public function updatePengiriman(Request $request, $id)
    {
        $supplier = $this->getSupplier();
        $po = PurchaseOrder::where('supplier_id', $supplier->id)->findOrFail($id);

        $request->validate([
            'status' => ['required', Rule::in([
                PurchaseOrderDeliveryUpdate::STATUS_PREPARING,
                PurchaseOrderDeliveryUpdate::STATUS_SHIPPED,
                PurchaseOrderDeliveryUpdate::STATUS_IN_TRANSIT,
            ])],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $po) {
            $status = $request->status;
            
            // If shipped, update PO main status to shipped
            if ($status === PurchaseOrderDeliveryUpdate::STATUS_SHIPPED) {
                $po->update(['status' => PurchaseOrder::STATUS_SHIPPED]);
            }

            PurchaseOrderDeliveryUpdate::create([
                'purchase_order_id' => $po->id,
                'updated_by' => auth()->id(),
                'status' => $status,
                'tracking_number' => $request->tracking_number,
                'description' => $request->description,
            ]);

            // Notify Owner
            $owner = User::where('role', 'pemilik')->first();
            if ($owner) {
                $notifType = ($status === PurchaseOrderDeliveryUpdate::STATUS_SHIPPED) ? Notification::TYPE_PO_SHIPPED : Notification::TYPE_GENERAL;
                Notification::send(
                    $owner->id,
                    $notifType,
                    'Update Pengiriman PO: ' . $po->po_number,
                    "Status PO {$po->po_number}: " . (PurchaseOrderDeliveryUpdate::statusLabels()[$status] ?? $status) . ". Keterangan: {$request->description}",
                    $po
                );
            }
        });

        return redirect()->route('pemasok.dashboard')->with('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function stok()
    {
        $supplier = $this->getSupplier();
        $materials = $supplier->rawMaterials()->withTimestamps()->get();
        return view('pemasok.stok', compact('materials', 'supplier'));
    }

    public function updateStok(Request $request, $materialId)
    {
        $supplier = $this->getSupplier();
        $request->validate([
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'minimum_order_qty' => ['required', 'numeric', 'min:0'],
            'available_stock' => ['required', 'numeric', 'min:0'],
            'lead_time_days' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        $supplier->rawMaterials()->updateExistingPivot($materialId, [
            'price_per_unit' => $request->price_per_unit,
            'minimum_order_qty' => $request->minimum_order_qty,
            'available_stock' => $request->available_stock,
            'lead_time_days' => $request->lead_time_days,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('pemasok.stok')->with('success', 'Stok dan harga penawaran berhasil diperbarui.');
    }
}
