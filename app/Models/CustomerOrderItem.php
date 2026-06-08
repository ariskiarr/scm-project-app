<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'quantity' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /** Hitung subtotal otomatis */
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->price * $this->quantity;
    }
}
