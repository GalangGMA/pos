<?php

namespace App\Filament\Resources\MKategoriResource\Pages;

use App\Filament\Resources\MKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMKategori extends EditRecord
{
    protected static string $resource = MKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
