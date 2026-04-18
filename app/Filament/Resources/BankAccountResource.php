<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use Filament\Tables\Table;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Rekening Transfer';
    protected static ?string $pluralLabel = 'Rekening Transfer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('bank_name')
                    ->label('Nama Bank')
                    ->required()
                    ->maxLength(50)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state)),  // ⬅️ otomatis uppercase saat save


                TextInput::make('account_number')
                    ->label('Nomor Rekening')
                    ->required()
                    ->maxLength(50),

                TextInput::make('account_holder')
                    ->label('Nama Pemilik')
                    ->required()
                    ->maxLength(100),

                Textarea::make('note')
                    ->label('Catatan')
                    ->columnSpan('full')
                    ->rows(3),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bank_name')->label('Bank')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('account_number')->label('No. Rekening')->searchable(),
                Tables\Columns\TextColumn::make('account_holder')->label('Pemilik')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Update Terakhir')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Rekening Aktif')
                    ->query(fn ($query) => $query->where('is_active', true)),
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
            'index' => Pages\ListBankAccount::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
