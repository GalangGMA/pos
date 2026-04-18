<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppVersionResource\Pages;
use App\Models\AppVersion;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// FORM
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;

// TABLE
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class AppVersionResource extends Resource
{
    protected static ?string $model = AppVersion::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'App Version';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Select::make('platform')
                ->options([
                    'android' => 'Android',
                    'ios' => 'iOS',
                ])
                ->required()
                ->reactive(),

            TextInput::make('version')
                ->placeholder('1.2.3')
                ->required(),

            TextInput::make('minimum_version')
                ->placeholder('1.2.3')
                ->helperText('Kosongkan jika sama dengan version.'),

            Toggle::make('force_update')
                ->label('Force Update'),

            TextInput::make('apk_url')
                ->placeholder('application/version/apk')
                ->required(),

            Textarea::make('message')
                ->label('Changelog')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('platform')
                    ->badge()
                    ->color(fn (string $state) =>
                        $state === 'android' ? 'success' : 'info'
                    ),

                TextColumn::make('version')
                    ->sortable(),

                TextColumn::make('minimum_version')
                    ->sortable(),

                IconColumn::make('force_update')
                    ->boolean(),


                TextColumn::make('apk_url')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppVersions::route('/'),
            'create' => Pages\CreateAppVersion::route('/create'),
            'edit' => Pages\EditAppVersion::route('/{record}/edit'),
        ];
    }
}
