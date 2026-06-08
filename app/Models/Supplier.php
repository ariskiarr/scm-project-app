<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────

    /** Akun user terkait (role pemasok) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Bahan baku yang disediakan pemasok ini (pivot) */
    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'supplier_raw_materials')
                    ->withPivot('price_per_unit', 'minimum_order_qty', 'available_stock', 'lead_time_days', 'is_active')
                    ->withTimestamps();
    }

    /** Bahan baku aktif yang disediakan pemasok ini */
    public function activeRawMaterials()
    {
        return $this->rawMaterials()->wherePivot('is_active', true);
    }

    /** Purchase Order yang dikirim ke pemasok ini */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /** Purchase Order yang masih aktif / belum selesai */
    public function activePurchaseOrders()
    {
        return $this->purchaseOrders()->whereNotIn('status', ['received', 'cancelled']);
    }
}
