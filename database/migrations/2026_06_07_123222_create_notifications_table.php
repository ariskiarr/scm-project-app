<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notifikasi sistem: stok menipis, konfirmasi PO, dsb.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', [
                'low_stock',            // stok bahan baku menipis
                'po_confirmed',         // PO dikonfirmasi pemasok
                'po_shipped',           // PO dikirim pemasok
                'po_received',          // PO diterima
                'order_confirmed',      // pesanan pelanggan dikonfirmasi
                'order_shipped',        // pesanan pelanggan dikirim
                'order_delivered',      // pesanan pelanggan diterima
                'new_order',            // pesanan baru masuk (untuk kasir)
                'general',
            ]);
            $table->string('title');
            $table->text('message');
            $table->string('reference_type')->nullable();   // model terkait (e.g. PurchaseOrder)
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
