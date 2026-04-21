<?php

namespace App\Filament\Resources\OutletResource\Pages;

use App\Filament\Resources\OutletResource;
use App\Support\TenantPlanGate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOutlet extends CreateRecord
{
    protected static string $resource = OutletResource::class;

    public function mount(): void
    {
        parent::mount();

        $tenantId = Auth::user()?->tenant_id;

        if ($tenantId) {
            app(TenantPlanGate::class)->assertCanCreateOutlet($tenantId);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Auth::user()?->tenant_id;

        return $data;
    }
}
