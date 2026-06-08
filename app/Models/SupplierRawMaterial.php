<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SupplierRawMaterial extends Model
{
    use HasFactory;
    protected $table = 'supplier_raw_materials';

    public $incrementing = true;

    protected $fillable = [
        'supplier_id',
        'raw_material_id',
        'price_per_unit',
        'minimum_order_qty',
        'available_stock',
        'lead_time_days',
        'is_active',
    ];

    protected $casts = [
        'price_per_unit'    => 'decimal:2',
        'minimum_order_qty' => 'decimal:2',
        'available_stock'   => 'decimal:2',
        'lead_time_days'    => 'integer',
        'is_active'         => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
