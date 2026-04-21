<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'SaaS';
    protected static ?string $navigationLabel = 'Subscription';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('tenant_id')
                ->label('Tenant')
                ->relationship('tenant', 'name')
                ->searchable()
                ->preload()
                ->disabled(fn () => Auth::user()?->role !== 'super_admin')
                ->required(),

            Select::make('plan_id')
                ->relationship('plan', 'name')
                ->searchable()
                ->preload()
                ->disabled(fn () => Auth::user()?->role !== 'super_admin')
                ->required(),

            Select::make('status')
                ->options([
                    'trial' => 'Trial',
                    'active' => 'Active',
                    'expired' => 'Expired',
                    'suspended' => 'Suspended',
                ])
                ->disabled(fn () => Auth::user()?->role !== 'super_admin')
                ->required(),

            DateTimePicker::make('starts_at')->disabled(fn () => Auth::user()?->role !== 'super_admin'),
            DateTimePicker::make('ends_at')->disabled(fn () => Auth::user()?->role !== 'super_admin'),
            DateTimePicker::make('trial_ends_at')->disabled(fn () => Auth::user()?->role !== 'super_admin'),
            DateTimePicker::make('grace_ends_at')->disabled(fn () => Auth::user()?->role !== 'super_admin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('plan.name')->label('Plan')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'trial',
                        'success' => 'active',
                        'danger' => ['expired', 'suspended'],
                    ]),
                Tables\Columns\TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('ends_at')->label('Berakhir')->placeholder('-')->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('trial_ends_at')->label('Akhir Trial')->placeholder('-')->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => Auth::user()?->role === 'super_admin'),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('plan');
        $authUser = Auth::user();

        if (! $authUser instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($authUser->role === 'super_admin') {
            return $query->with('tenant')->latest('id');
        }

        return $query->where('tenant_id', $authUser->tenant_id)->latest('id');
    }

    public static function canViewAny(): bool
    {
        $authUser = Auth::user();

        return $authUser instanceof User && in_array($authUser->role, ['super_admin', 'owner'], true);
    }

    public static function canCreate(): bool
    {
        $authUser = Auth::user();

        return $authUser instanceof User && $authUser->role === 'super_admin';
    }

    public static function canEdit($record): bool
    {
        $authUser = Auth::user();

        return $authUser instanceof User && $authUser->role === 'super_admin';
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
