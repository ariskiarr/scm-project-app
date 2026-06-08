<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'unit',
        'current_stock',
        'minimum_stock',
        'price_per_unit',
        'is_active',
    ];

    protected $casts = [
        'current_stock'  => 'decimal:2',
        'minimum_stock'  => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    // ─── Accessors ───────────────────────────────────────────────

    /** Cek apakah stok sudah mencapai atau di bawah batas minimum */
    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /** Selisih stok saat ini dengan batas minimum */
    public function getStockDeficitAttribute(): float
    {
        return max(0, $this->minimum_stock - $this->current_stock);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /** Hanya bahan baku aktif */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Bahan baku yang stoknya menipis */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    // ─── Relationships ───────────────────────────────────────────

    /** Pemasok yang menyediakan bahan baku ini (pivot) */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_raw_materials')
                    ->withPivot('price_per_unit', 'minimum_order_qty', 'available_stock', 'lead_time_days', 'is_active')
                    ->withTimestamps();
    }

    /** Item pada Purchase Order yang memuat bahan baku ini */
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /** Riwayat mutasi stok bahan baku ini */
    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }
}
