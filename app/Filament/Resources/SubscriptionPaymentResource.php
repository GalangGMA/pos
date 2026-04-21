<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPaymentResource\Pages;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Support\BillingService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubscriptionPaymentResource extends Resource
{
    protected static ?string $model = SubscriptionPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Billing';
    protected static ?string $navigationLabel = 'Subscription Payments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('subscription_id')
                ->label('Subscription')
                ->options(function () {
                    $authUser = Auth::user();

                    $query = Subscription::query()->with(['tenant', 'plan'])->latest('id');

                    if ($authUser instanceof User && $authUser->role !== 'super_admin') {
                        $query->where('tenant_id', $authUser->tenant_id);
                    }

                    return $query->get()->mapWithKeys(fn (Subscription $subscription) => [
                        $subscription->id => sprintf(
                            '%s - %s (%s)',
                            $subscription->tenant?->name ?? 'Tenant',
                            $subscription->plan?->name ?? 'Plan',
                            $subscription->status
                        ),
                    ]);
                })
                ->searchable()
                ->preload()
                ->disabled(fn () => Auth::user()?->role !== 'super_admin')
                ->required(),

            TextInput::make('invoice_no')
                ->label('Invoice No')
                ->disabled()
                ->dehydrated(false)
                ->placeholder('Otomatis dibuat saat simpan'),

            TextInput::make('amount')
                ->numeric()
                ->required()
                ->disabled(fn () => Auth::user()?->role !== 'super_admin'),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'submitted' => 'Submitted',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->required()
                ->disabled(fn () => Auth::user()?->role !== 'super_admin'),

            TextInput::make('payment_method')
                ->default('manual_transfer')
                ->required()
                ->disabled(fn () => Auth::user()?->role !== 'super_admin'),

            TextInput::make('reference_no')
                ->maxLength(255),

            DateTimePicker::make('due_at')
                ->disabled(fn () => Auth::user()?->role !== 'super_admin'),

            DateTimePicker::make('paid_at')
                ->disabled(fn () => Auth::user()?->role !== 'super_admin'),

            Textarea::make('notes')
                ->rows(3),

            FileUpload::make('proof_of_payment')
                ->label('Bukti Pembayaran')
                ->image()
                ->directory('subscription-payments')
                ->visibility('public'),

            Textarea::make('rejection_reason')
                ->rows(3)
                ->disabled(fn () => Auth::user()?->role !== 'super_admin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')->label('Invoice')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('subscription.tenant.name')->label('Tenant')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('subscription.plan.name')->label('Plan')->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('IDR', true),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => ['pending', 'submitted'],
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('due_at')->dateTime('d M Y H:i')->placeholder('-'),
                Tables\Columns\TextColumn::make('paid_at')->dateTime('d M Y H:i')->placeholder('-'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SubscriptionPayment $record) => Auth::user()?->role === 'super_admin' && $record->status === 'submitted')
                    ->action(function (SubscriptionPayment $record) {
                        app(BillingService::class)->approvePayment($record, Auth::user());

                        Notification::make()->title('Pembayaran disetujui.')->success()->send();
                    }),
                Action::make('reject')
                    ->color('danger')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan penolakan')
                            ->required(),
                    ])
                    ->visible(fn (SubscriptionPayment $record) => Auth::user()?->role === 'super_admin' && $record->status === 'submitted')
                    ->action(function (SubscriptionPayment $record, array $data) {
                        app(BillingService::class)->rejectPayment($record, Auth::user(), $data['rejection_reason'] ?? null);

                        Notification::make()->title('Pembayaran ditolak.')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $authUser = Auth::user();
        $query = parent::getEloquentQuery()->with(['subscription.tenant', 'subscription.plan']);

        if (! $authUser instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($authUser->role === 'super_admin') {
            return $query->latest('id');
        }

        if ($authUser->role === 'owner') {
            return $query->whereHas('subscription', fn ($subQuery) => $subQuery->where('tenant_id', $authUser->tenant_id))->latest('id');
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        $authUser = Auth::user();

        return $authUser instanceof User && in_array($authUser->role, ['super_admin', 'owner'], true);
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->role === 'super_admin';
    }

    public static function canEdit($record): bool
    {
        $authUser = Auth::user();

        if (! $authUser instanceof User) {
            return false;
        }

        if ($authUser->role === 'super_admin') {
            return true;
        }

        return $authUser->role === 'owner' && $record instanceof SubscriptionPayment && in_array($record->status, ['pending', 'rejected'], true);
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->role === 'super_admin';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionPayments::route('/'),
            'create' => Pages\CreateSubscriptionPayment::route('/create'),
            'edit' => Pages\EditSubscriptionPayment::route('/{record}/edit'),
        ];
    }
}
