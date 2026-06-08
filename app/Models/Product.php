<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'price',
        'stock',
        'unit',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'stock'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // ─── Relationships ───────────────────────────────────────────

    public function orderItems()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    /** Pesanan pelanggan yang memuat produk ini */
    public function customerOrders()
    {
        return $this->belongsToMany(CustomerOrder::class, 'customer_order_items')
                    ->withPivot('quantity', 'price', 'subtotal')
                    ->withTimestamps();
    }
}
