<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySalesSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'summary_date',
        'total_transactions',
        'total_orders_online',
        'total_orders_offline',
        'gross_sales',
        'total_discount',
        'total_shipping_cost',
        'net_sales',
        'cancelled_orders',
        'generated_by',
    ];

    protected $casts = [
        'summary_date'         => 'date',
        'gross_sales'          => 'decimal:2',
        'total_discount'       => 'decimal:2',
        'total_shipping_cost'  => 'decimal:2',
        'net_sales'            => 'decimal:2',
    ];

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeForDate($query, $date)
    {
        return $query->where('summary_date', $date);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('summary_date', $year)
                     ->whereMonth('summary_date', $month);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('summary_date', $year);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Generate atau update rekap untuk tanggal tertentu dari data CustomerOrder.
     */
    public static function generateForDate(\Carbon\Carbon|string $date, ?int $generatedBy = null): self
    {
        $date = \Carbon\Carbon::parse($date)->toDateString();

        $orders = CustomerOrder::whereDate('created_at', $date)
            ->whereNotIn('status', [CustomerOrder::STATUS_CANCELLED])
            ->get();

        $cancelled = CustomerOrder::whereDate('created_at', $date)
            ->where('status', CustomerOrder::STATUS_CANCELLED)
            ->count();

        $data = [
            'total_transactions'   => $orders->count(),
            'total_orders_online'  => $orders->where('order_type', 'online')->count(),
            'total_orders_offline' => $orders->where('order_type', 'offline')->count(),
            'gross_sales'          => $orders->sum('subtotal'),
            'total_discount'       => $orders->sum('discount'),
            'total_shipping_cost'  => $orders->sum('shipping_cost'),
            'net_sales'            => $orders->sum('total_amount'),
            'cancelled_orders'     => $cancelled,
            'generated_by'         => $generatedBy,
        ];

        return self::updateOrCreate(['summary_date' => $date], $data);
    }

    // ─── Relationships ───────────────────────────────────────────

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
