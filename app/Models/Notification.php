<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'reference_type',
        'reference_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ─── Type Constants ───────────────────────────────────────────

    const TYPE_LOW_STOCK       = 'low_stock';
    const TYPE_PO_CONFIRMED    = 'po_confirmed';
    const TYPE_PO_SHIPPED      = 'po_shipped';
    const TYPE_PO_RECEIVED     = 'po_received';
    const TYPE_ORDER_CONFIRMED = 'order_confirmed';
    const TYPE_ORDER_SHIPPED   = 'order_shipped';
    const TYPE_ORDER_DELIVERED = 'order_delivered';
    const TYPE_NEW_ORDER       = 'new_order';
    const TYPE_GENERAL         = 'general';

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /** Tandai notifikasi sebagai sudah dibaca */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Buat notifikasi baru untuk satu user.
     *
     * @param int    $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param mixed  $reference  Model Eloquent terkait (opsional)
     */
    public static function send(
        int    $userId,
        string $type,
        string $title,
        string $message,
        mixed  $reference = null
    ): self {
        return self::create([
            'user_id'        => $userId,
            'type'           => $type,
            'title'          => $title,
            'message'        => $message,
            'reference_type' => $reference ? class_basename($reference) : null,
            'reference_id'   => $reference?->id,
        ]);
    }

    // ─── Relationships ───────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi polimorfik ke model referensi */
    public function reference()
    {
        if (! $this->reference_type || ! $this->reference_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . $this->reference_type;

        return class_exists($modelClass)
            ? $modelClass::find($this->reference_id)
            : null;
    }
}
