<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Outlet;
use App\Models\Tenant;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'User Management';

    public static function form(Form $form): Form
    {
        $authUser = Auth::user();
        $tenantId = $authUser?->tenant_id;

        return $form
            ->schema([
                Select::make('tenant_id')
                    ->label('Tenant')
                    ->options(fn () => Tenant::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn () => $authUser instanceof User && $authUser->role === 'super_admin')
                    ->required(fn () => $authUser instanceof User && $authUser->role === 'super_admin')
                    ->default($tenantId)
                    ->dehydrated(fn () => $authUser instanceof User && $authUser->role === 'super_admin'),

                Hidden::make('tenant_id')
                    ->default($tenantId)
                    ->visible(fn () => ! ($authUser instanceof User && $authUser->role === 'super_admin'))
                    ->dehydrated(),

                TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->label('Password')
                    ->maxLength(255),

                Select::make('role')
                    ->options(function () use ($authUser) {
                        if (! $authUser instanceof User) {
                            return [];
                        }

                        if ($authUser->role === 'owner') {
                            return [
                                'owner' => 'Owner',
                                'admin_toko' => 'Admin Toko',
                                'kasir' => 'Kasir',
                            ];
                        }

                        if ($authUser->role === 'super_admin') {
                            return [
                                'super_admin' => 'Superadmin',
                                'owner' => 'Owner',
                                'admin_toko' => 'Admin Toko',
                                'kasir' => 'Kasir',
                            ];
                        }

                        return [
                            'admin_toko' => 'Admin Toko',
                            'kasir' => 'Kasir',
                        ];
                    })
                    ->default('kasir')
                    ->required()
                    ->live(),

                Select::make('outlet_id')
                    ->label('Outlet')
                    ->options(fn () => Outlet::query()
                        ->where('tenant_id', $tenantId)
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('role') === 'kasir')
                    ->required(fn (Get $get) => $get('role') === 'kasir')
                    ->helperText('Kasir wajib terhubung ke satu outlet aktif.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'primary' => 'super_admin',
                        'danger' => 'owner',
                        'success' => 'admin_toko',
                        'warning' => 'kasir',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Created'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record) => static::canManageRecord($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => static::canManageRecord($record) && $record->id !== Auth::id()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['tenant', 'outlet']);
        $authUser = Auth::user();

        if (! $authUser instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($authUser->role === 'super_admin') {
            return $query;
        }

        $query->where('tenant_id', $authUser->tenant_id);

        if ($authUser->role !== 'owner') {
            $query->where('role', '!=', 'owner');
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        $authUser = Auth::user();

        return $authUser instanceof User
            && in_array($authUser->role, ['super_admin', 'owner', 'admin_toko'], true);
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return $record instanceof User && static::canManageRecord($record);
    }

    public static function canDelete($record): bool
    {
        return $record instanceof User
            && static::canManageRecord($record)
            && $record->id !== Auth::id();
    }

    protected static function canManageRecord(User $record): bool
    {
        $authUser = Auth::user();

        if (! $authUser instanceof User) {
            return false;
        }

        if ($authUser->role === 'super_admin') {
            return true;
        }

        if ($authUser->tenant_id !== $record->tenant_id) {
            return false;
        }

        if ($authUser->role === 'owner') {
            return true;
        }

        return $authUser->role === 'admin_toko' && $record->role !== 'owner';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
