<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Support\TenantPlanGate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function mount(): void
    {
        parent::mount();

        $tenantId = Auth::user()?->tenant_id;

        if ($tenantId) {
            app(TenantPlanGate::class)->assertCanCreateUser($tenantId);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Auth::user()?->role !== 'super_admin') {
            $data['tenant_id'] = Auth::user()?->tenant_id;
        }

        if (! empty($data['tenant_id'])) {
            app(TenantPlanGate::class)->assertCanCreateUser((int) $data['tenant_id']);
        }

        if (($data['role'] ?? null) !== 'kasir') {
            $data['outlet_id'] = null;
        }

        return $data;
    }
}
