<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rekap penjualan harian (di-generate otomatis / manual).
     * Digunakan untuk dashboard pemilik & rekap kasir.
     */
    public function up(): void
    {
        Schema::create('daily_sales_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('summary_date')->unique();
            $table->integer('total_transactions')->default(0);
            $table->integer('total_orders_online')->default(0);
            $table->integer('total_orders_offline')->default(0);
            $table->decimal('gross_sales', 14, 2)->default(0);
            $table->decimal('total_discount', 14, 2)->default(0);
            $table->decimal('total_shipping_cost', 14, 2)->default(0);
            $table->decimal('net_sales', 14, 2)->default(0);
            $table->integer('cancelled_orders')->default(0);
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales_summaries');
    }
};
