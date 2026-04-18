<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Transaksi;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;


class DailyTransactionReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?string $navigationLabel = 'Laporan Harian Transaksi';
    protected static string $view = 'filament.pages.daily-transaction-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaksi::query())
            ->columns([
                TextColumn::make('no_transaksi')->label('No Transaksi'),
                TextColumn::make('tanggal')->label('Tanggal')->dateTime('d-m-Y H:i'),
                TextColumn::make('total')->label('Total')->money('IDR', true),
                TextColumn::make('bayar')->label('Bayar')->money('IDR', true),
                TextColumn::make('kembalian')->label('Kembalian')->money('IDR', true),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->label('Filter Tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    }),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('preview')
                ->label('Preview')
                ->defaultFormat('print')
                ->fileName('laporan-harian-' . now()->format('Y-m-d')),

                // Export / Print single action (format selector akan tersedia)
                FilamentExportHeaderAction::make('export')
                    ->label('Export / Print')
                    ->fileName('laporan-harian-' . now()->format('Y-m-d'))
                    ->defaultFormat('pdf'), // default format: 'pdf', 'xlsx', 'csv', 'print'
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('bulk-export')
                    ->label('Export Data Terpilih')
                    ->fileName('laporan-terpilih-' . now()->format('Y-m-d'))
                    ->defaultFormat('xlsx'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->paginated(false)
            ->striped();
    }

    public function getHeading(): string
    {
        return "Laporan Harian Transaksi";
    }
}
