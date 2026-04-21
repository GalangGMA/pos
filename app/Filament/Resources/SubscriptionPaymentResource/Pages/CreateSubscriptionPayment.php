<?php

namespace App\Filament\Resources\SubscriptionPaymentResource\Pages;

use App\Filament\Resources\SubscriptionPaymentResource;
use App\Models\Subscription;
use App\Support\BillingService;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriptionPayment extends CreateRecord
{
    protected static string $resource = SubscriptionPaymentResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $subscription = Subscription::query()->findOrFail($data['subscription_id']);

        return app(BillingService::class)->createInvoice($subscription, $data);
    }
}
