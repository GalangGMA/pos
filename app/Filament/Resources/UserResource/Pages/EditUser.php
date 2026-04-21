<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (Auth::user()?->role !== 'super_admin') {
            $data['tenant_id'] = $this->record->tenant_id ?: Auth::user()?->tenant_id;
        }

        if (($data['role'] ?? null) !== 'kasir') {
            $data['outlet_id'] = null;
        }

        return $data;
    }
}
