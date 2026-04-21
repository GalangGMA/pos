<?php

namespace App\Support;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BillingService
{
    public function createInvoice(Subscription $subscription, array $attributes = []): SubscriptionPayment
    {
        return DB::transaction(function () use ($subscription, $attributes) {
            $subscription->loadMissing('plan');

            $invoiceNo = $attributes['invoice_no'] ?? $this->generateInvoiceNo();

            return SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'invoice_no' => $invoiceNo,
                'amount' => $attributes['amount'] ?? ($subscription->plan?->price ?? 0),
                'status' => $attributes['status'] ?? 'pending',
                'payment_method' => $attributes['payment_method'] ?? 'manual_transfer',
                'reference_no' => $attributes['reference_no'] ?? null,
                'notes' => $attributes['notes'] ?? null,
                'due_at' => $attributes['due_at'] ?? now()->addDays(7),
                'paid_at' => $attributes['paid_at'] ?? null,
            ]);
        });
    }

    public function submitManualPayment(SubscriptionPayment $payment, array $attributes = []): SubscriptionPayment
    {
        if (! in_array($payment->status, ['pending', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'payment' => ['Tagihan ini tidak bisa diajukan ulang.'],
            ]);
        }

        if (($attributes['proof_of_payment'] ?? null) instanceof UploadedFile) {
            $attributes['proof_of_payment'] = $attributes['proof_of_payment']->store('subscription-payments', 'public');
        }

        $payment->update([
            'status' => 'submitted',
            'reference_no' => $attributes['reference_no'] ?? $payment->reference_no,
            'notes' => $attributes['notes'] ?? $payment->notes,
            'proof_of_payment' => $attributes['proof_of_payment'] ?? $payment->proof_of_payment,
            'paid_at' => $attributes['paid_at'] ?? now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
            'rejection_reason' => null,
        ]);

        return $payment->fresh(['subscription.plan', 'subscription.tenant']);
    }

    public function approvePayment(SubscriptionPayment $payment, User $reviewer): SubscriptionPayment
    {
        return DB::transaction(function () use ($payment, $reviewer) {
            $payment->loadMissing('subscription.plan');

            $payment->update([
                'status' => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
                'paid_at' => $payment->paid_at ?: now(),
            ]);

            $this->activateSubscriptionFromPayment($payment);

            return $payment->fresh(['subscription.plan', 'subscription.tenant', 'reviewer']);
        });
    }

    public function rejectPayment(SubscriptionPayment $payment, User $reviewer, ?string $reason = null): SubscriptionPayment
    {
        $payment->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $payment->fresh(['subscription.plan', 'subscription.tenant', 'reviewer']);
    }

    private function activateSubscriptionFromPayment(SubscriptionPayment $payment): void
    {
        $subscription = $payment->subscription;

        if (! $subscription) {
            return;
        }

        $startsAt = now();
        $endsAt = $subscription->ends_at && $subscription->ends_at->isFuture()
            ? $subscription->ends_at->copy()->addMonth()
            : $startsAt->copy()->addMonth();

        $subscription->update([
            'status' => 'active',
            'starts_at' => $subscription->starts_at ?: $startsAt,
            'ends_at' => $endsAt,
            'trial_ends_at' => null,
            'grace_ends_at' => null,
        ]);
    }

    private function generateInvoiceNo(): string
    {
        do {
            $invoiceNo = 'INV-SUB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (SubscriptionPayment::query()->where('invoice_no', $invoiceNo)->exists());

        return $invoiceNo;
    }
}
