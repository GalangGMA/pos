<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Outlet;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ResolvesTenantContext
{
    protected function currentUser(Request $request): User
    {
        $user = $request->user();

        if (! $user instanceof User) {
            throw ValidationException::withMessages([
                'auth' => ['User tidak valid.'],
            ]);
        }

        return $user;
    }

    protected function currentTenant(Request $request): Tenant
    {
        $tenant = $this->currentUser($request)->tenant;

        if (! $tenant instanceof Tenant) {
            throw ValidationException::withMessages([
                'tenant' => ['Tenant tidak ditemukan untuk user ini.'],
            ]);
        }

        return $tenant;
    }

    protected function accessibleOutlets(User $user): Collection
    {
        if ($user->role === 'super_admin' || ! $user->tenant_id) {
            return new Collection();
        }

        if ($user->role === 'kasir') {
            return Outlet::query()
                ->where('tenant_id', $user->tenant_id)
                ->whereKey($user->outlet_id)
                ->where('is_active', true)
                ->get();
        }

        return Outlet::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    protected function currentOutlet(Request $request, bool $required = true): ?Outlet
    {
        $user = $this->currentUser($request);

        if ($user->role === 'kasir') {
            $outlet = Outlet::query()
                ->where('tenant_id', $user->tenant_id)
                ->whereKey($user->outlet_id)
                ->where('is_active', true)
                ->first();

            if (! $outlet && $required) {
                throw ValidationException::withMessages([
                    'outlet' => ['Outlet kasir tidak valid atau tidak aktif.'],
                ]);
            }

            return $outlet;
        }

        $outletId = $request->header('X-Outlet-Id');

        if (! $outletId) {
            if ($required) {
                throw ValidationException::withMessages([
                    'outlet' => ['Header X-Outlet-Id wajib dikirim untuk user ini.'],
                ]);
            }

            return null;
        }

        $outlet = Outlet::query()
            ->where('tenant_id', $user->tenant_id)
            ->whereKey($outletId)
            ->where('is_active', true)
            ->first();

        if (! $outlet && $required) {
            throw ValidationException::withMessages([
                'outlet' => ['Outlet tidak ditemukan atau tidak bisa diakses.'],
            ]);
        }

        return $outlet;
    }

    protected function currentSubscription(User $user): ?Subscription
    {
        if (! $user->tenant_id) {
            return null;
        }

        return Subscription::query()
            ->with('plan')
            ->where('tenant_id', $user->tenant_id)
            ->latest('id')
            ->first();
    }

    protected function permissionsFor(User $user): array
    {
        return [
            'manage_billing' => $user->role === 'owner',
            'manage_platform' => $user->role === 'super_admin',
            'manage_users' => in_array($user->role, ['owner', 'admin_toko'], true),
            'create_admin_toko' => in_array($user->role, ['owner', 'admin_toko'], true),
            'manage_products' => in_array($user->role, ['owner', 'admin_toko'], true),
            'view_reports' => in_array($user->role, ['owner', 'admin_toko'], true),
            'switch_outlet' => in_array($user->role, ['owner', 'admin_toko'], true),
        ];
    }
}
