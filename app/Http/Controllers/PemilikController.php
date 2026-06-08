<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderDeliveryUpdate;
use App\Models\CustomerOrder;
use App\Models\DailySalesSummary;
use App\Models\StockMutation;
use App\Models\User;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PemilikController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        // 1. Stock Summary
        $materials = RawMaterial::all();
        $lowStockMaterials = RawMaterial::lowStock()->get();

        // Auto-generate low-stock notifications
        foreach ($lowStockMaterials as $mat) {
            $existingNotif = Notification::where('user_id', $user->id)
                ->where('type', Notification::TYPE_LOW_STOCK)
                ->where('reference_id', $mat->id)
                ->where('created_at', '>=', now()->subDay())
                ->first();

            if (!$existingNotif) {
                Notification::send(
                    $user->id,
                    Notification::TYPE_LOW_STOCK,
                    'Stok Menipis: ' . $mat->name,
                    "Stok bahan baku {$mat->name} saat ini {$mat->current_stock} {$mat->unit}, berada di bawah batas minimum ({$mat->minimum_stock} {$mat->unit}).",
                    $mat
                );
            }
        }

        // 2. Active PO Delivery Status
        $activePOs = PurchaseOrder::with(['supplier', 'latestDeliveryUpdate'])->active()->latest()->get();

        // 3. Daily Sales Total
        $todayRevenue = CustomerOrder::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->where('status', '!=', CustomerOrder::STATUS_CANCELLED)
            ->sum('total_amount');

        $todayOrdersCount = CustomerOrder::whereDate('created_at', today())
            ->where('status', '!=', CustomerOrder::STATUS_CANCELLED)
            ->count();

        return view('pemilik.dashboard', compact('materials', 'lowStockMaterials', 'activePOs', 'todayRevenue', 'todayOrdersCount'));
    }

    // --- MANAJEMEN BAHAN BAKU ---
    public function bahanBaku()
    {
        $materials = RawMaterial::latest()->get();
        return view('pemilik.bahan-baku', compact('materials'));
    }

    public function storeBahanBaku(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:raw_materials'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $material = RawMaterial::create($request->all());

        // Create initial stock mutation if current stock > 0
        if ($material->current_stock > 0) {
            StockMutation::create([
                'raw_material_id' => $material->id,
                'created_by' => auth()->id(),
                'type' => StockMutation::TYPE_ADJUSTMENT,
                'quantity' => $material->current_stock,
                'stock_before' => 0,
                'stock_after' => $material->current_stock,
                'reference' => 'Initial Stock',
                'notes' => 'Stok awal saat pendaftaran bahan baku baru.',
            ]);
        }

        return redirect()->route('pemilik.bahan-baku')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function updateBahanBaku(Request $request, $id)
    {
        $material = RawMaterial::findOrFail($id);
        $request->validate([
            'code' => ['required', 'string', Rule::unique('raw_materials')->ignore($material->id)],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $material->update($request->only('code', 'name', 'unit', 'minimum_stock', 'price_per_unit', 'description', 'is_active'));

        return redirect()->route('pemilik.bahan-baku')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    // --- MANAJEMEN PEMASOK ---
    public function pemasok()
    {
        $suppliers = Supplier::with('rawMaterials')->latest()->get();
        $materials = RawMaterial::active()->get();
        // Get user pemasok who do not have supplier profile yet
        $pemasokUsers = User::where('role', 'pemasok')
            ->whereDoesntHave('supplier')
            ->get();

        return view('pemilik.pemasok', compact('suppliers', 'materials', 'pemasokUsers'));
    }

    public function storePemasok(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'bank_name' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'bank_account_name' => ['nullable', 'string'],
            'create_user' => ['required', 'boolean'],
            // validation if creating user
            'user_email' => ['required_if:create_user,1', 'nullable', 'email', 'unique:users,email'],
            'user_password' => ['required_if:create_user,1', 'nullable', 'min:6'],
        ]);

        DB::transaction(function () use ($request) {
            if ($request->create_user) {
                $user = User::create([
                    'name' => $request->company_name,
                    'email' => $request->user_email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'password' => Hash::make($request->user_password),
                    'role' => 'pemasok',
                    'is_active' => true,
                ]);
                $userId = $user->id;
            } else {
                $userId = $request->user_id;
            }

            Supplier::create([
                'user_id' => $userId,
                'company_name' => $request->company_name,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'email' => $request->email ?: ($request->create_user ? $request->user_email : null),
                'address' => $request->address,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_name' => $request->bank_account_name,
                'is_active' => true,
            ]);
        });

        return redirect()->route('pemilik.pemasok')->with('success', 'Pemasok berhasil ditambahkan.');
    }

    public function updatePemasok(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'bank_name' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'bank_account_name' => ['nullable', 'string'],
        ]);

        $supplier->update($request->all());

        return redirect()->route('pemilik.pemasok')->with('success', 'Pemasok berhasil diperbarui.');
    }

    public function linkRawMaterial(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'minimum_order_qty' => ['required', 'numeric', 'min:0'],
            'available_stock' => ['required', 'numeric', 'min:0'],
            'lead_time_days' => ['required', 'integer', 'min:1'],
        ]);

        $supplier->rawMaterials()->syncWithoutDetaching([
            $request->raw_material_id => [
                'price_per_unit' => $request->price_per_unit,
                'minimum_order_qty' => $request->minimum_order_qty,
                'available_stock' => $request->available_stock,
                'lead_time_days' => $request->lead_time_days,
                'is_active' => true
            ]
        ]);

        return redirect()->route('pemilik.pemasok')->with('success', 'Bahan baku berhasil dikaitkan ke pemasok.');
    }

    public function unlinkRawMaterial($supplierId, $materialId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $supplier->rawMaterials()->detach($materialId);
        return redirect()->route('pemilik.pemasok')->with('success', 'Kaitan bahan baku dilepas.');
    }

    // --- PURCHASE ORDER ---
    public function purchaseOrders()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'creator', 'items.rawMaterial', 'latestDeliveryUpdate'])->latest()->get();
        $suppliers = Supplier::where('is_active', true)->get();

        return view('pemilik.purchase-orders', compact('purchaseOrders', 'suppliers'));
    }

    public function createPO(Request $request)
    {
        $supplierId = $request->supplier_id;
        $supplier = Supplier::with('activeRawMaterials')->findOrFail($supplierId);

        return view('pemilik.purchase-orders-create', compact('supplier'));
    }

    public function storePO(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'expected_delivery_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.price_per_unit' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request) {
            $po_number = 'PO-' . now()->format('YmdHis') . '-' . rand(10, 99);

            $po = PurchaseOrder::create([
                'po_number' => $po_number,
                'supplier_id' => $request->supplier_id,
                'created_by' => auth()->id(),
                'status' => PurchaseOrder::STATUS_SENT,
                'total_amount' => 0,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
            ]);

            $total = 0;
            foreach ($request->items as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price_per_unit'];
                $total += $subtotal;

                $material = RawMaterial::find($itemData['raw_material_id']);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'raw_material_id' => $itemData['raw_material_id'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $material->unit,
                    'price_per_unit' => $itemData['price_per_unit'],
                    'subtotal' => $subtotal,
                    'received_quantity' => 0,
                ]);
            }

            $po->update(['total_amount' => $total]);

            // Add first delivery update
            PurchaseOrderDeliveryUpdate::create([
                'purchase_order_id' => $po->id,
                'updated_by' => auth()->id(),
                'status' => PurchaseOrderDeliveryUpdate::STATUS_PREPARING,
                'description' => 'Purchase order dibuat dan dikirim ke pemasok.',
            ]);

            // Notify supplier
            $supplier = Supplier::find($request->supplier_id);
            if ($supplier->user_id) {
                Notification::send(
                    $supplier->user_id,
                    Notification::TYPE_NEW_ORDER,
                    'Purchase Order Baru: ' . $po->po_number,
                    "Anda menerima purchase order baru dari Pemilik Kebab Berkah dengan total order Rp " . number_format($total, 0, ',', '.'),
                    $po
                );
            }
        });

        return redirect()->route('pemilik.purchase-orders')->with('success', 'Purchase Order berhasil dikirim ke pemasok.');
    }

    public function cancelPO($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if (!$po->is_cancellable) {
            return back()->withErrors(['error' => 'Purchase order ini tidak dapat dibatalkan pada status saat ini.']);
        }

        DB::transaction(function () use ($po) {
            $po->update(['status' => PurchaseOrder::STATUS_CANCELLED]);

            PurchaseOrderDeliveryUpdate::create([
                'purchase_order_id' => $po->id,
                'updated_by' => auth()->id(),
                'status' => PurchaseOrderDeliveryUpdate::STATUS_CANCELLED,
                'description' => 'Purchase order dibatalkan oleh pemilik.',
            ]);

            // Notify supplier
            if ($po->supplier->user_id) {
                Notification::send(
                    $po->supplier->user_id,
                    Notification::TYPE_GENERAL,
                    'Purchase Order Dibatalkan: ' . $po->po_number,
                    "Purchase order {$po->po_number} telah dibatalkan oleh pemilik usaha.",
                    $po
                );
            }
        });

        return redirect()->route('pemilik.purchase-orders')->with('success', 'Purchase Order dibatalkan.');
    }

    public function receivePO(Request $request, $id)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);
        if ($po->status === PurchaseOrder::STATUS_RECEIVED) {
            return back()->withErrors(['error' => 'PO sudah pernah diterima sebelumnya.']);
        }

        DB::transaction(function () use ($po) {
            $po->update([
                'status' => PurchaseOrder::STATUS_RECEIVED,
                'actual_delivery_date' => now(),
            ]);

            foreach ($po->items as $item) {
                $material = RawMaterial::find($item->raw_material_id);
                $stockBefore = $material->current_stock;

                // Increase stock
                $material->current_stock += $item->quantity;
                $material->save();

                // Save item received quantity
                $item->update(['received_quantity' => $item->quantity]);

                // Create stock mutation
                StockMutation::create([
                    'raw_material_id' => $material->id,
                    'created_by' => auth()->id(),
                    'purchase_order_id' => $po->id,
                    'type' => StockMutation::TYPE_IN,
                    'quantity' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $material->current_stock,
                    'reference' => 'PO Received: ' . $po->po_number,
                    'notes' => 'Penerimaan bahan baku melalui Purchase Order.',
                ]);
            }

            PurchaseOrderDeliveryUpdate::create([
                'purchase_order_id' => $po->id,
                'updated_by' => auth()->id(),
                'status' => PurchaseOrderDeliveryUpdate::STATUS_RECEIVED,
                'description' => 'Bahan baku telah diterima dengan baik oleh pemilik usaha, stok disesuaikan.',
            ]);

            // Notify supplier
            if ($po->supplier->user_id) {
                Notification::send(
                    $po->supplier->user_id,
                    Notification::TYPE_PO_RECEIVED,
                    'Bahan Baku Diterima: ' . $po->po_number,
                    "Pemilik usaha telah mengonfirmasi penerimaan barang untuk PO {$po->po_number}.",
                    $po
                );
            }
        });

        return redirect()->route('pemilik.purchase-orders')->with('success', 'Bahan baku berhasil diterima dan stok telah diperbarui.');
    }

    // --- LAPORAN PENJUALAN ---
    public function laporan(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now()->endOfDay();

        $summaries = DailySalesSummary::whereBetween('summary_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('summary_date', 'desc')
            ->get();

        $totalRevenue = $summaries->sum('net_sales');
        $totalTransactions = $summaries->sum('total_transactions');
        $totalOnline = $summaries->sum('total_orders_online');
        $totalOffline = $summaries->sum('total_orders_offline');

        return view('pemilik.laporan', compact('summaries', 'totalRevenue', 'totalTransactions', 'totalOnline', 'totalOffline', 'startDate', 'endDate'));
    }

    // --- MANAJEMEN PRODUK (CRUD) ---
    public function produk()
    {
        $products = Product::latest()->get();
        return view('pemilik.produk', compact('products'));
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'is_available' => ['boolean'],
        ]);

        Product::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'is_available' => $request->boolean('is_available', true),
        ]);

        return redirect()->route('pemilik.produk')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function updateProduk(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'code' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('products')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'is_available' => ['boolean'],
        ]);

        $product->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'is_available' => $request->boolean('is_available', true),
        ]);

        return redirect()->route('pemilik.produk')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroyProduk($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // soft delete

        return redirect()->route('pemilik.produk')->with('success', 'Produk berhasil dihapus.');
    }

    // --- MANAJEMEN AKUN ---
    public function akun()
    {
        $users = User::where('role', '!=', 'pelanggan')->orderBy('name')->get();
        return view('pemilik.akun', compact('users'));
    }

    public function storeAkun(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'role' => ['required', Rule::in(['pemilik', 'kasir', 'kurir', 'pemasok', 'pelanggan'])],
            'password' => ['required', 'min:6'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('pemilik.akun')->with('success', 'Akun pengguna berhasil didaftarkan.');
    }

    public function updateAkun(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'role' => ['required', Rule::in(['pemilik', 'kasir', 'kurir', 'pemasok', 'pelanggan'])],
        ]);

        $data = $request->only('name', 'email', 'phone', 'address', 'role', 'is_active');
        if ($request->filled('password')) {
            $request->validate(['password' => ['min:6']]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('pemilik.akun')->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function readAllNotifications()
    {
        Notification::where('user_id', auth()->id())->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}
