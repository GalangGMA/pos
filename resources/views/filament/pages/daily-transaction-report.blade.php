<x-filament-panels::page>

    @php
        $totalOmzet = \App\Models\Transaksi::query()
            ->when(request('tableFilters.tanggal.from') ?? null, fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
            ->when(request('tableFilters.tanggal.until') ?? null, fn ($q, $date) => $q->whereDate('tanggal', '<=', $date))
            ->sum('total');
    @endphp

    <div class="text-xl font-bold mb-4">
        Total Omzet: Rp {{ number_format($totalOmzet, 0, ',', '.') }}
    </div>

    {{ $this->table }}
</x-filament-panels::page>
