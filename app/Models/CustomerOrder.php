<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'kasir_id',
        'kurir_id',
        'order_type',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping_cost',
        'discount',
        'total_amount',
        'shipping_address',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount'      => 'decimal:2',
        'total_amount'  => 'decimal:2',
        'paid_at'       => 'datetime',
    ];

    // ─── Status Constants ─────────────────────────────────────────

    const STATUS_PENDING       = 'pending';
    const STATUS_CONFIRMED     = 'confirmed';
    const STATUS_PROCESSING    = 'processing';
    const STATUS_READY_TO_SHIP = 'ready_to_ship';
    const STATUS_SHIPPED       = 'shipped';
    const STATUS_DELIVERED     = 'delivered';
    const STATUS_CANCELLED     = 'cancelled';

    const PAYMENT_CASH     = 'cash';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_QRIS     = 'qris';
    const PAYMENT_COD      = 'cod';

    const PAYMENT_STATUS_UNPAID   = 'unpaid';
    const PAYMENT_STATUS_PAID     = 'paid';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING       => 'Menunggu Konfirmasi',
            self::STATUS_CONFIRMED     => 'Dikonfirmasi',
            self::STATUS_PROCESSING    => 'Diproses',
            self::STATUS_READY_TO_SHIP => 'Siap Kirim',
            self::STATUS_SHIPPED       => 'Dikirim',
            self::STATUS_DELIVERED     => 'Diterima',
            self::STATUS_CANCELLED     => 'Dibatalkan',
        ];
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function getIsCancellableAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopePending($query)      { return $query->where('status', self::STATUS_PENDING); }
    public function scopeConfirmed($query)    { return $query->where('status', self::STATUS_CONFIRMED); }
    public function scopeReadyToShip($query)  { return $query->where('status', self::STATUS_READY_TO_SHIP); }
    public function scopeShipped($query)      { return $query->where('status', self::STATUS_SHIPPED); }
    public function scopeDelivered($query)    { return $query->where('status', self::STATUS_DELIVERED); }
    public function scopeToday($query)        { return $query->whereDate('created_at', today()); }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    // ─── Relationships ───────────────────────────────────────────

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }

    public function items()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function deliveryUpdates()
    {
        return $this->hasMany(OrderDeliveryUpdate::class);
    }

    /** Update pengiriman terbaru */
    public function latestDeliveryUpdate()
    {
        return $this->hasOne(OrderDeliveryUpdate::class)->latestOfMany();
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /** Hitung ulang subtotal, diskon, dan total dari item */
    public function recalculateTotal(): void
    {
        $this->subtotal     = $this->items()->sum('subtotal');
        $this->total_amount = $this->subtotal + $this->shipping_cost - $this->discount;
        $this->save();
    }

    /** Tandai order sebagai sudah dibayar */
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => self::PAYMENT_STATUS_PAID,
            'paid_at'        => now(),
        ]);
    }
}
