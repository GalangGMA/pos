<?php

namespace App\Filament\Pages;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\Page;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Panel;


class MonthlyTransactionDetail extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = null; // tidak muncul di sidebar
    protected static string $view = 'filament.pages.monthly-transaction-detail';

    protected static bool $shouldRegisterNavigation = false;


    public static function getRouteName(?string $panel = null): string
    {
        return 'filament.pages.monthly-transaction-detail';
    }


    public ?string $month = null; // parameter bulan dari URL

    public function mount(string $month)
    {
        $this->month = $month;
    }

    public function getTitle(): string
    {
        return 'Transaksi Bulan ' . Carbon::parse($this->month . '-01')->translatedFormat('F Y');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaksi::query()
                    ->whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$this->month])
                    ->orderBy('tanggal', 'desc')
            )
            ->columns([
                TextColumn::make('no_transaksi')->label('No Transaksi')->searchable(),
                TextColumn::make('tanggal')->dateTime('d M Y H:i'),
                TextColumn::make('total')->money('IDR', true),
                TextColumn::make('bayar')->money('IDR', true),
                TextColumn::make('kembalian')->money('IDR', true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->paginated(true);
    }
}
