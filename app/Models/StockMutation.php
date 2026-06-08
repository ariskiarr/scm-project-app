<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'created_by',
        'purchase_order_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference',
        'notes',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after'  => 'decimal:2',
    ];

    // ─── Type Constants ───────────────────────────────────────────

    const TYPE_IN         = 'in';
    const TYPE_OUT        = 'out';
    const TYPE_ADJUSTMENT = 'adjustment';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_IN         => 'Stok Masuk',
            self::TYPE_OUT        => 'Stok Keluar',
            self::TYPE_ADJUSTMENT => 'Penyesuaian',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeIn($query)         { return $query->where('type', self::TYPE_IN); }
    public function scopeOut($query)        { return $query->where('type', self::TYPE_OUT); }
    public function scopeAdjustment($query) { return $query->where('type', self::TYPE_ADJUSTMENT); }

    // ─── Relationships ───────────────────────────────────────────

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
