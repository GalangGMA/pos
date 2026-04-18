<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Transaksi;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class MonthlyTransactionReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?string $navigationLabel = 'Laporan Bulanan';
    protected static string $view = 'filament.pages.monthly-transaction-report';

    /**
     * FIX utama untuk error getTableRecordKey()
     * Gunakan bulan sebagai unique key karena query tidak memiliki id
     */
    public function getTableRecordKey(mixed $record): string
    {
        return $record->bulan;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaksi::query()
                    ->select(
                        DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as bulan'),
                        DB::raw('SUM(total) as total_omzet'),
                        DB::raw('SUM(bayar) as total_bayar'),
                        DB::raw('SUM(kembalian) as total_kembalian'),
                        DB::raw('COUNT(*) as jumlah_transaksi')
                    )
                    ->groupBy('bulan')
                    ->orderBy('bulan', 'desc')
            )
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export Data')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->columns([
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn ($state) =>
                        \Carbon\Carbon::parse($state . '-01')->translatedFormat('F Y')
                    )
                    ->url(fn ($record) =>
                        route('filament.pages.monthly-transaction-detail', ['month' => $record->bulan])
                    )
                    ->openUrlInNewTab(),

                TextColumn::make('jumlah_transaksi')
                    ->label('Jumlah Transaksi')
                    ->sortable(),

                TextColumn::make('total_omzet')
                    ->label('Total Omzet')
                    ->money('IDR', true)
                    ->sortable(),

                // TextColumn::make('total_bayar')
                //     ->label('Total Pembayaran')
                //     ->money('IDR', true),

                // TextColumn::make('total_kembalian')
                //     ->label('Total Kembalian')
                //     ->money('IDR', true),
            ])
            ->paginated(false)
            ->striped();
    }

    public function getHeading(): string
    {
        return 'Laporan Bulanan Transaksi';
    }
}
