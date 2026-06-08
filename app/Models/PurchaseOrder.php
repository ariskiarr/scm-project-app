<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'created_by',
        'status',
        'total_amount',
        'expected_delivery_date',
        'actual_delivery_date',
        'notes',
    ];

    protected $casts = [
        'total_amount'           => 'decimal:2',
        'expected_delivery_date' => 'date',
        'actual_delivery_date'   => 'date',
    ];

    // ─── Status Constants ─────────────────────────────────────────

    const STATUS_DRAFT     = 'draft';
    const STATUS_SENT      = 'sent';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SHIPPED   = 'shipped';
    const STATUS_RECEIVED  = 'received';
    const STATUS_CANCELLED = 'cancelled';

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeDraft($query)     { return $query->where('status', self::STATUS_DRAFT); }
    public function scopeSent($query)      { return $query->where('status', self::STATUS_SENT); }
    public function scopeConfirmed($query) { return $query->where('status', self::STATUS_CONFIRMED); }
    public function scopeShipped($query)   { return $query->where('status', self::STATUS_SHIPPED); }
    public function scopeReceived($query)  { return $query->where('status', self::STATUS_RECEIVED); }
    public function scopeCancelled($query) { return $query->where('status', self::STATUS_CANCELLED); }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_RECEIVED, self::STATUS_CANCELLED]);
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function getIsEditableAttribute(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getIsCancellableAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT]);
    }

    // ─── Relationships ───────────────────────────────────────────

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function deliveryUpdates()
    {
        return $this->hasMany(PurchaseOrderDeliveryUpdate::class);
    }

    /** Update pengiriman terbaru */
    public function latestDeliveryUpdate()
    {
        return $this->hasOne(PurchaseOrderDeliveryUpdate::class)->latestOfMany();
    }

    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /** Hitung ulang total dari item dan simpan */
    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('subtotal');
        $this->save();
    }
}
