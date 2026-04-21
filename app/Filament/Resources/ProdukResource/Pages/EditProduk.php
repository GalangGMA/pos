<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use App\Filament\Resources\ProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProduk extends EditRecord
{
    protected static string $resource = ProdukResource::class;

    protected array $outletStocks = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['outlet_stocks'] = $this->record->stocks()
            ->with('outlet:id,name')
            ->get()
            ->map(fn ($stock) => [
                'outlet_id' => $stock->outlet_id,
                'stock' => $stock->stock,
                'minimum_stock' => $stock->minimum_stock,
            ])
            ->all();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->outletStocks = $data['outlet_stocks'] ?? [];
        unset($data['outlet_stocks']);

        $data['tenant_id'] = $this->record->tenant_id ?: Auth::user()?->tenant_id;
        $data['stok'] = collect($this->outletStocks)->sum(fn ($item) => (int) ($item['stock'] ?? 0));

        return $data;
    }

    protected function afterSave(): void
    {
        ProdukResource::syncOutletStocks($this->record, $this->outletStocks, $this->record->tenant_id ?: Auth::user()?->tenant_id);
    }
}
