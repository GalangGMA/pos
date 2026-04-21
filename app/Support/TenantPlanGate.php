<?php

namespace App\Support;

use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class TenantPlanGate
{
    public function latestSubscription(int $tenantId): ?Subscription
    {
        return Subscription::query()
            ->with('plan')
            ->where('tenant_id', $tenantId)
            ->latest('id')
            ->first();
    }

    public function assertSubscriptionIsOperational(Tenant $tenant): void
    {
        if ($tenant->status !== 'active') {
            throw ValidationException::withMessages([
                'subscription' => ['Tenant tidak aktif. Hubungi administrator platform.'],
            ]);
        }

        $subscription = $this->latestSubscription($tenant->id);

        if (! $subscription || ! $subscription->plan) {
            throw ValidationException::withMessages([
                'subscription' => ['Langganan tenant belum dikonfigurasi.'],
            ]);
        }

        $now = now();

        if ($subscription->status === 'suspended') {
            throw ValidationException::withMessages([
                'subscription' => ['Langganan sedang ditangguhkan.'],
            ]);
        }

        if ($subscription->status === 'expired') {
            $graceEndsAt = $subscription->grace_ends_at;

            if (! $graceEndsAt || $graceEndsAt->isPast()) {
                throw ValidationException::withMessages([
                    'subscription' => ['Langganan sudah berakhir. Silakan perpanjang paket terlebih dahulu.'],
                ]);
            }
        }

        if ($subscription->ends_at && $subscription->ends_at->isPast() && (! $subscription->grace_ends_at || $subscription->grace_ends_at->isPast())) {
            throw ValidationException::withMessages([
                'subscription' => ['Masa aktif langganan sudah habis.'],
            ]);
        }

        if ($subscription->status === 'trial' && $subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
            throw ValidationException::withMessages([
                'subscription' => ['Masa trial sudah berakhir.'],
            ]);
        }
    }

    public function assertCanCreateOutlet(int $tenantId): void
    {
        $subscription = $this->requireSubscriptionWithPlan($tenantId);
        $currentCount = Outlet::query()->where('tenant_id', $tenantId)->count();

        if ($currentCount >= (int) $subscription->plan->max_outlets) {
            throw ValidationException::withMessages([
                'plan' => ['Batas outlet pada paket aktif sudah tercapai.'],
            ]);
        }
    }

    public function assertCanCreateUser(int $tenantId): void
    {
        $subscription = $this->requireSubscriptionWithPlan($tenantId);
        $currentCount = User::query()->where('tenant_id', $tenantId)->count();

        if ($currentCount >= (int) $subscription->plan->max_users) {
            throw ValidationException::withMessages([
                'plan' => ['Batas user pada paket aktif sudah tercapai.'],
            ]);
        }
    }

    public function assertCanCreateProduct(int $tenantId): void
    {
        $subscription = $this->requireSubscriptionWithPlan($tenantId);
        $currentCount = Produk::query()->where('tenant_id', $tenantId)->count();

        if ($currentCount >= (int) $subscription->plan->max_products) {
            throw ValidationException::withMessages([
                'plan' => ['Batas produk pada paket aktif sudah tercapai.'],
            ]);
        }
    }

    public function hasFeature(int $tenantId, string $feature, bool $default = true): bool
    {
        $subscription = $this->requireSubscriptionWithPlan($tenantId);
        $features = $subscription->plan->features ?? [];

        if (! is_array($features)) {
            return $default;
        }

        if (! array_key_exists($feature, $features)) {
            return $default;
        }

        return (bool) $features[$feature];
    }

    public function assertFeatureEnabled(Tenant $tenant, string $feature, ?string $message = null, bool $default = true): void
    {
        if (! $this->hasFeature($tenant->id, $feature, $default)) {
            throw ValidationException::withMessages([
                'plan_feature' => [$message ?: 'Fitur ini tidak tersedia pada paket aktif tenant.'],
            ]);
        }
    }

    private function requireSubscriptionWithPlan(int $tenantId): Subscription
    {
        $subscription = $this->latestSubscription($tenantId);

        if (! $subscription || ! $subscription->plan) {
            throw ValidationException::withMessages([
                'subscription' => ['Paket tenant belum tersedia.'],
            ]);
        }

        return $subscription;
    }
}
