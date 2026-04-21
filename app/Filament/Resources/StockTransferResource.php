<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTransferResource\Pages;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\StockTransfer;
use App\Models\User;
use App\Support\StockTransferService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StockTransferResource extends Resource
{
    protected static ?string $model = StockTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Transfer Stok';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('produk_id')
                ->label('Produk')
                ->options(fn () => Produk::query()
                    ->where('tenant_id', Auth::user()?->tenant_id)
                    ->orderBy('nama')
                    ->pluck('nama', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Select::make('from_outlet_id')
                ->label('Dari Outlet')
                ->options(fn () => Outlet::query()
                    ->where('tenant_id', Auth::user()?->tenant_id)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Select::make('to_outlet_id')
                ->label('Ke Outlet')
                ->options(fn () => Outlet::query()
                    ->where('tenant_id', Auth::user()?->tenant_id)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('quantity')
                ->label('Jumlah Transfer')
                ->numeric()
                ->required(),

            Textarea::make('notes')
                ->label('Catatan')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produk.nama')->label('Produk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('fromOutlet.name')->label('Dari')->sortable(),
                Tables\Columns\TextColumn::make('toOutlet.name')->label('Ke')->sortable(),
                Tables\Columns\TextColumn::make('quantity')->label('Qty')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'completed']),
                Tables\Columns\TextColumn::make('transferredBy.name')->label('Oleh')->placeholder('-'),
                Tables\Columns\TextColumn::make('completed_at')->label('Tanggal')->dateTime('d M Y H:i')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat_catatan')
                    ->label('Lihat Catatan')
                    ->modalHeading('Catatan Transfer')
                    ->modalContent(fn (StockTransfer $record) => view('filament.modals.stock-transfer-notes', ['record' => $record]))
                    ->visible(fn (StockTransfer $record) => filled($record->notes)),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $authUser = Auth::user();
        $query = parent::getEloquentQuery()->with(['produk', 'fromOutlet', 'toOutlet', 'transferredBy']);

        if (! $authUser instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($authUser->role === 'super_admin') {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('tenant_id', $authUser->tenant_id)->latest('id');
    }

    public static function canViewAny(): bool
    {
        $authUser = Auth::user();

        return $authUser instanceof User && in_array($authUser->role, ['owner', 'admin_toko'], true);
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockTransfers::route('/'),
            'create' => Pages\CreateStockTransfer::route('/create'),
        ];
    }
}
