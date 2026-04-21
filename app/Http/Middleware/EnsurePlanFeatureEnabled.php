<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\TenantPlanGate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanFeatureEnabled
{
    public function __construct(private TenantPlanGate $tenantPlanGate)
    {
    }

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if ($user instanceof User && $user->tenant) {
            $messages = [
                'mobile_access' => 'Paket aktif tenant belum mendukung akses mobile.',
                'reports' => 'Paket aktif tenant belum mendukung akses laporan.',
                'multi_outlet' => 'Paket aktif tenant belum mendukung multi outlet.',
            ];

            $this->tenantPlanGate->assertFeatureEnabled(
                $user->tenant,
                $feature,
                $messages[$feature] ?? null,
            );
        }

        return $next($request);
    }
}
