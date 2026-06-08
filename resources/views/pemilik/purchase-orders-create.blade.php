@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="poForm()">
    <div class="mb-8">
        <a href="{{ route('pemilik.purchase-orders') }}" class="text-xs text-amber-600 font-bold hover:text-amber-700">&larr; Kembali ke Daftar PO</a>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-2">Buat Purchase Order Baru</h1>
        <p class="text-slate-500 text-sm mt-1">Mengirim PO ke pemasok: <span class="font-bold text-slate-700">{{ $supplier->company_name }}</span></p>
    </div>

    <form action="{{ route('pemilik.purchase-orders.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

        <!-- PO Form Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Pilih Bahan Baku</h3>

                <div class="space-y-4">
                    @forelse($supplier->activeRawMaterials as $index => $mat)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl gap-4"
                             x-data="{ active: false, qty: {{ floatval($mat->pivot->minimum_order_qty) }}, price: {{ $mat->pivot->price_per_unit }} }">

                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="items[{{ $index }}][checked]" value="1" x-model="active" @change="calculateTotal()"
                                       class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="font-bold text-sm text-slate-800 block">{{ $mat->name }}</span>
                                    <span class="text-2xs text-slate-400 font-mono block">Kode: {{ $mat->code }}</span>
                                    <span class="text-3xs text-purple-600 font-bold uppercase block mt-0.5">Min. Order: {{ floatval($mat->pivot->minimum_order_qty) }} {{ $mat->unit }}</span>
                                </div>
                            </div>

                            <!-- Input Qty and Price (Only enabled when checked) -->
                            <div class="flex items-center gap-3" x-show="active">
                                <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $mat->id }}">

                                <div>
                                    <label class="block text-3xs font-bold text-slate-400 uppercase tracking-wider">Jumlah</label>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <input type="number" step="0.01" name="items[{{ $index }}][quantity]"
                                               x-model.number="qty" @input="calculateTotal()" min="{{ floatval($mat->pivot->minimum_order_qty) }}"
                                               class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center focus:outline-none focus:ring-1 focus:ring-amber-500">
                                        <span class="text-xs text-slate-400 font-medium">{{ $mat->unit }}</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-3xs font-bold text-slate-400 uppercase tracking-wider">Harga per {{ $mat->unit }}</label>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span class="text-xs text-slate-400">Rp</span>
                                        <input type="number" name="items[{{ $index }}][price_per_unit]"
                                               x-model.number="price" @input="calculateTotal()" readonly
                                               class="w-24 bg-slate-100 rounded-lg border border-slate-100 px-2 py-1 text-xs text-right text-slate-500 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-amber-50 p-6 text-center rounded-2xl border border-amber-100">
                            <p class="text-sm text-amber-700">Pemasok ini belum memiliki katalog bahan baku aktif.</p>
                            <a href="{{ route('pemilik.pemasok') }}" class="text-xs text-amber-900 font-bold underline mt-2 block">Kelola Pemasok & Bahan Baku &rarr;</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- PO Summary Panel -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 sticky top-24">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Informasi Pengiriman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Estimasi Tanggal Tiba</label>
                        <input type="date" name="expected_delivery_date" required min="{{ now()->toDateString() }}"
                               class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-2xs font-bold text-slate-500 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="Masukkan instruksi khusus..."></textarea>
                    </div>

                    <div class="border-t border-slate-50 pt-4">
                        <div class="flex justify-between items-center text-sm mb-2 text-slate-500 font-medium">
                            <span>Estimasi Total PO</span>
                            <span class="text-lg font-bold text-slate-800">Rp <span x-text="formatRupiah(totalAmount)">0</span></span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="totalAmount <= 0"
                                class="w-full flex justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/10 hover:bg-amber-600 focus:outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            Kirim Purchase Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function poForm() {
        return {
            totalAmount: 0,
            calculateTotal() {
                let sum = 0;
                // Parse checked checkboxes and inputs
                const items = document.querySelectorAll('input[type="checkbox"][name*="[checked]"]:checked');
                items.forEach(checkbox => {
                    const parent = checkbox.closest('[x-data]');
                    if (parent) {
                        const qtyInput = parent.querySelector('input[name*="[quantity]"]');
                        const priceInput = parent.querySelector('input[name*="[price_per_unit]"]');
                        if (qtyInput && priceInput) {
                            const qty = parseFloat(qtyInput.value) || 0;
                            const price = parseFloat(priceInput.value) || 0;
                            sum += qty * price;
                        }
                    }
                });
                this.totalAmount = sum;
            },
            formatRupiah(val) {
                return new Intl.NumberFormat('id-ID').format(val);
            }
        }
    }
</script>
@endsection
