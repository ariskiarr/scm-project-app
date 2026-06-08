<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('kasir_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('kurir_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('order_type', ['online', 'offline'])->default('online');
            $table->enum('status', [
                'pending',          // menunggu konfirmasi
                'confirmed',        // dikonfirmasi kasir
                'processing',       // sedang diproses
                'ready_to_ship',    // siap kirim
                'shipped',          // dalam pengiriman
                'delivered',        // sudah diterima
                'cancelled',        // dibatalkan
            ])->default('pending');
            $table->enum('payment_method', [
                'cash',
                'transfer',
                'qris',
                'cod',
            ]);
            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'refunded',
            ])->default('unpaid');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};
