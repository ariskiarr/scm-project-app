<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDeliveryUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'updated_by',
        'status',
        'description',
        'tracking_number',
    ];

    // ─── Status Constants ─────────────────────────────────────────

    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PREPARING  = 'preparing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_RECEIVED   = 'received';
    const STATUS_CANCELLED  = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_CONFIRMED  => 'Dikonfirmasi',
            self::STATUS_PREPARING  => 'Sedang Disiapkan',
            self::STATUS_SHIPPED    => 'Dikirim',
            self::STATUS_IN_TRANSIT => 'Dalam Perjalanan',
            self::STATUS_RECEIVED   => 'Diterima',
            self::STATUS_CANCELLED  => 'Dibatalkan',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    // ─── Relationships ───────────────────────────────────────────

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
