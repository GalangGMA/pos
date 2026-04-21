<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MKategoriResource\Pages;
use App\Models\MKategori;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MKategoriResource extends Resource
{
    protected static ?string $model = MKategori::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('tenant_id')
                ->default(Auth::user()?->tenant_id)
                ->dehydrated(),

            TextInput::make('nama')
                ->label('Nama Kategori')
                ->required()
                ->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Nama Kategori')->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMKategoris::route('/'),
            'create' => Pages\CreateMKategori::route('/create'),
            'edit' => Pages\EditMKategori::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $authUser = Auth::user();

        if (! $authUser instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('tenant_id', $authUser->tenant_id);
    }
}
