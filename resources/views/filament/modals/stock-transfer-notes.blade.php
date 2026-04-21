<div class="space-y-2">
    <div class="text-sm text-gray-500">{{ $record->produk?->nama }}: {{ $record->fromOutlet?->name }} -> {{ $record->toOutlet?->name }}</div>
    <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-900">
        {{ $record->notes }}
    </div>
</div>
