<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'address',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ─── Role Helpers ────────────────────────────────────────────

    public function isPemilik(): bool   { return $this->role === 'pemilik'; }
    public function isKasir(): bool     { return $this->role === 'kasir'; }
    public function isPemasok(): bool   { return $this->role === 'pemasok'; }
    public function isKurir(): bool     { return $this->role === 'kurir'; }
    public function isPelanggan(): bool { return $this->role === 'pelanggan'; }

    // ─── Relationships ───────────────────────────────────────────

    /** Profil pemasok (hanya untuk role pemasok) */
    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    /** Purchase Order yang dibuat oleh user ini (pemilik/kasir) */
    public function createdPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    /** Pesanan pelanggan milik user ini (role pelanggan) */
    public function customerOrders()
    {
        return $this->hasMany(CustomerOrder::class, 'customer_id');
    }

    /** Pesanan yang ditangani kasir ini */
    public function handledOrders()
    {
        return $this->hasMany(CustomerOrder::class, 'kasir_id');
    }

    /** Pesanan yang dikirim kurir ini */
    public function deliveredOrders()
    {
        return $this->hasMany(CustomerOrder::class, 'kurir_id');
    }

    /** Update pengiriman PO yang dilakukan user ini */
    public function purchaseOrderDeliveryUpdates()
    {
        return $this->hasMany(PurchaseOrderDeliveryUpdate::class, 'updated_by');
    }

    /** Update pengiriman pesanan pelanggan oleh kurir */
    public function orderDeliveryUpdates()
    {
        return $this->hasMany(OrderDeliveryUpdate::class, 'updated_by');
    }

    /** Mutasi stok yang dicatat user ini */
    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class, 'created_by');
    }

    /** Notifikasi milik user ini */
    public function appNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    /** Notifikasi yang belum dibaca */
    public function unreadNotifications()
    {
        return $this->appNotifications()->where('is_read', false);
    }

    /** Rekap penjualan harian yang di-generate user ini */
    public function dailySalesSummaries()
    {
        return $this->hasMany(DailySalesSummary::class, 'generated_by');
    }
}
