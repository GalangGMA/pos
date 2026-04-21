<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesTenantContext;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Support\BillingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BillingController extends Controller
{
    use ResolvesTenantContext;

    public function __construct(private BillingService $billingService)
    {
    }

    public function summary(Request $request)
    {
        $user = $this->currentUser($request);
        $this->ensureOwnerAccess($user);

        $subscription = $this->currentSubscription($user);
        $payments = SubscriptionPayment::query()
            ->with(['subscription.plan'])
            ->whereHas('subscription', fn ($query) => $query->where('tenant_id', $user->tenant_id))
            ->latest('id')
            ->get();

        return response()->json([
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at?->toISOString(),
                'ends_at' => $subscription->ends_at?->toISOString(),
                'trial_ends_at' => $subscription->trial_ends_at?->toISOString(),
                'grace_ends_at' => $subscription->grace_ends_at?->toISOString(),
                'plan' => $subscription->plan ? [
                    'id' => $subscription->plan->id,
                    'name' => $subscription->plan->name,
                    'code' => $subscription->plan->code,
                    'price' => $subscription->plan->price,
                    'max_outlets' => $subscription->plan->max_outlets,
                    'max_users' => $subscription->plan->max_users,
                    'max_products' => $subscription->plan->max_products,
                    'features' => $subscription->plan->features ?? [],
                ] : null,
            ] : null,
            'payments' => $payments->map(fn (SubscriptionPayment $payment) => $this->transformPayment($payment))->values()->all(),
        ]);
    }

    public function submit(Request $request, SubscriptionPayment $payment)
    {
        $user = $this->currentUser($request);
        $this->ensureOwnerAccess($user);

        $payment->loadMissing('subscription');

        if ($payment->subscription?->tenant_id !== $user->tenant_id) {
            throw ValidationException::withMessages([
                'payment' => ['Tagihan tidak ditemukan untuk tenant ini.'],
            ]);
        }

        $validated = $request->validate([
            'reference_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'proof_of_payment' => 'nullable|image|max:4096',
        ]);

        $payment = $this->billingService->submitManualPayment($payment, $validated);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil dikirim.',
            'payment' => $this->transformPayment($payment),
        ]);
    }

    private function ensureOwnerAccess(User $user): void
    {
        if ($user->role !== 'owner') {
            throw ValidationException::withMessages([
                'billing' => ['Hanya owner yang bisa mengakses billing.'],
            ]);
        }
    }

    private function transformPayment(SubscriptionPayment $payment): array
    {
        return [
            'id' => $payment->id,
            'invoice_no' => $payment->invoice_no,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'reference_no' => $payment->reference_no,
            'notes' => $payment->notes,
            'proof_of_payment_url' => $payment->proof_of_payment ? asset('storage/' . ltrim($payment->proof_of_payment, '/')) : null,
            'paid_at' => $payment->paid_at?->toISOString(),
            'due_at' => $payment->due_at?->toISOString(),
            'reviewed_at' => $payment->reviewed_at?->toISOString(),
            'rejection_reason' => $payment->rejection_reason,
            'plan_name' => $payment->subscription?->plan?->name,
        ];
    }
}
