<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\MKategori;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(100),

                Select::make('kategori')
                    ->label('Kategori')
                    ->options(MKategori::all()->pluck('nama', 'nama'))
                    ->searchable()
                    ->required(),

                FileUpload::make('image')
                    ->label('Gambar Produk')
                    ->image()
                    ->directory('produk-images')
                    ->visibility('public'),

                TextInput::make('harga')
                    ->label('Harga (Rp)')
                    ->numeric()
                    ->required(),

                TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Gambar')->square(),
                TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
                TextColumn::make('kategori')->label('Kategori')->sortable(),
                TextColumn::make('harga')->label('Harga')->money('IDR', true),
                TextColumn::make('stok')->label('Stok')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
