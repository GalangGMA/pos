<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\TenantPlanGate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionIsActive
{
    public function __construct(private TenantPlanGate $tenantPlanGate)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && $user->tenant) {
            $this->tenantPlanGate->assertSubscriptionIsOperational($user->tenant);
        }

        return $next($request);
    }
}
