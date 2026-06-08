<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'raw_material_id',
        'quantity',
        'unit',
        'price_per_unit',
        'subtotal',
        'received_quantity',
    ];

    protected $casts = [
        'quantity'          => 'decimal:2',
        'price_per_unit'    => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'received_quantity' => 'decimal:2',
    ];

    // ─── Accessors ───────────────────────────────────────────────

    /** Sisa kuantitas yang belum diterima */
    public function getPendingQuantityAttribute(): float
    {
        return max(0, $this->quantity - $this->received_quantity);
    }

    /** Apakah seluruh item sudah diterima */
    public function getIsFullyReceivedAttribute(): bool
    {
        return $this->received_quantity >= $this->quantity;
    }

    // ─── Relationships ───────────────────────────────────────────

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /** Hitung subtotal otomatis dari quantity × price_per_unit */
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->quantity * $this->price_per_unit;
    }
}
