<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\MKategori;
use App\Models\Outlet;
use App\Models\ProductOutlet;
use App\Models\ProductStock;
use App\Models\Produk;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
                Hidden::make('tenant_id')
                    ->default(Auth::user()?->tenant_id)
                    ->dehydrated(),

                TextInput::make('nama')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(100),

                Select::make('kategori')
                    ->label('Kategori')
                    ->options(fn () => MKategori::query()
                        ->where('tenant_id', Auth::user()?->tenant_id)
                        ->orderBy('nama')
                        ->pluck('nama', 'nama'))
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

                Repeater::make('outlet_stocks')
                    ->label('Distribusi Outlet')
                    ->schema([
                        Select::make('outlet_id')
                            ->label('Outlet')
                            ->options(fn () => Outlet::query()
                                ->where('tenant_id', Auth::user()?->tenant_id)
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->searchable(),

                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('minimum_stock')
                            ->label('Minimum Stok')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->defaultItems(1)
                    ->columns(3)
                    ->reorderable(false)
                    ->addActionLabel('Tambah Outlet')
                    ->dehydrated(false)
                    ->helperText('Tentukan outlet yang boleh menjual produk ini beserta stoknya.'),

                TextInput::make('stok')
                    ->label('Total Stok')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn (?Model $record, Get $get) => $record?->stocks?->sum('stock') ?? collect($get('outlet_stocks'))->sum(fn ($item) => (int) ($item['stock'] ?? 0))),
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
                TextColumn::make('stocks_sum_stock')
                    ->label('Total Stok')
                    ->sortable(),
                TextColumn::make('outletMappings_count')
                    ->label('Outlet Aktif')
                    ->sortable(),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withCount('outletMappings')->withSum('stocks', 'stock');
        $authUser = Auth::user();

        if (! $authUser instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('tenant_id', $authUser->tenant_id);
    }

    public static function syncOutletStocks(Produk $produk, array $outletStocks, ?int $tenantId = null): void
    {
        $tenantId ??= $produk->tenant_id;

        $normalizedStocks = collect($outletStocks)
            ->map(function (array $item) {
                return [
                    'outlet_id' => (int) ($item['outlet_id'] ?? 0),
                    'stock' => (int) ($item['stock'] ?? 0),
                    'minimum_stock' => (int) ($item['minimum_stock'] ?? 0),
                ];
            })
            ->filter(fn (array $item) => $item['outlet_id'] > 0)
            ->unique('outlet_id')
            ->values();

        $validOutletIds = Outlet::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('id', $normalizedStocks->pluck('outlet_id'))
            ->pluck('id');

        ProductOutlet::query()->where('produk_id', $produk->id)->delete();
        ProductStock::query()->where('produk_id', $produk->id)->delete();

        foreach ($normalizedStocks as $item) {
            if (! $validOutletIds->contains($item['outlet_id'])) {
                continue;
            }

            ProductOutlet::create([
                'tenant_id' => $tenantId,
                'produk_id' => $produk->id,
                'outlet_id' => $item['outlet_id'],
            ]);

            ProductStock::create([
                'tenant_id' => $tenantId,
                'produk_id' => $produk->id,
                'outlet_id' => $item['outlet_id'],
                'stock' => $item['stock'],
                'minimum_stock' => $item['minimum_stock'],
            ]);
        }

        $produk->updateQuietly([
            'stok' => $normalizedStocks->sum('stock'),
        ]);
    }
}
