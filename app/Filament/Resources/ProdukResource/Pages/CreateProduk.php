<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use App\Filament\Resources\ProdukResource;
use App\Support\TenantPlanGate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProduk extends CreateRecord
{
    protected static string $resource = ProdukResource::class;

    protected array $outletStocks = [];

    public function mount(): void
    {
        parent::mount();

        $tenantId = Auth::user()?->tenant_id;

        if ($tenantId) {
            app(TenantPlanGate::class)->assertCanCreateProduct($tenantId);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->outletStocks = $data['outlet_stocks'] ?? [];
        unset($data['outlet_stocks']);

        $data['tenant_id'] = Auth::user()?->tenant_id;
        $data['stok'] = collect($this->outletStocks)->sum(fn ($item) => (int) ($item['stock'] ?? 0));

        return $data;
    }

    protected function afterCreate(): void
    {
        ProdukResource::syncOutletStocks($this->record, $this->outletStocks, Auth::user()?->tenant_id);
    }
}
