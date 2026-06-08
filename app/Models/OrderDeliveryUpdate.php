<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDeliveryUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'updated_by',
        'status',
        'location',
        'description',
    ];

    // ─── Status Constants ─────────────────────────────────────────

    const STATUS_PENDING         = 'pending';
    const STATUS_CONFIRMED       = 'confirmed';
    const STATUS_PROCESSING      = 'processing';
    const STATUS_READY_TO_SHIP   = 'ready_to_ship';
    const STATUS_PICKED_UP       = 'picked_up';
    const STATUS_IN_TRANSIT      = 'in_transit';
    const STATUS_DELIVERED       = 'delivered';
    const STATUS_FAILED_DELIVERY = 'failed_delivery';
    const STATUS_CANCELLED       = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING         => 'Menunggu Konfirmasi',
            self::STATUS_CONFIRMED       => 'Dikonfirmasi',
            self::STATUS_PROCESSING      => 'Diproses',
            self::STATUS_READY_TO_SHIP   => 'Siap Dikirim',
            self::STATUS_PICKED_UP       => 'Diambil Kurir',
            self::STATUS_IN_TRANSIT      => 'Dalam Perjalanan',
            self::STATUS_DELIVERED       => 'Terkirim',
            self::STATUS_FAILED_DELIVERY => 'Gagal Dikirim',
            self::STATUS_CANCELLED       => 'Dibatalkan',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    // ─── Relationships ───────────────────────────────────────────

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
