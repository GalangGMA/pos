<x-filament::page>
    <x-filament::section>
        <div>
            <h2 class="text-xl font-bold mb-2">
                Detail Transaksi Bulan {{ \Carbon\Carbon::parse($this->month . '-01')->translatedFormat('F Y') }}
            </h2>
            <p class="text-sm text-gray-600">
                Klik kolom transaksi untuk melihat detail produk nanti.
            </p>
        </div>
    </x-filament::section>

    {{ $this->table }}
</x-filament::page>
