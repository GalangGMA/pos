<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ResolvesTenantContext;
use App\Models\Outlet;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Support\TenantPlanGate;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ResolvesTenantContext;

    public function __construct(private TenantPlanGate $tenantPlanGate)
    {
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'outlet_name' => 'nullable|string|max:255',
            'username' => 'required|string|max:100|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $payload = DB::transaction(function () use ($request) {
            $businessCode = Str::slug($request->business_name) ?: 'business';
            $baseCode = $businessCode;
            $suffix = 1;

            while (Tenant::where('business_code', $businessCode)->exists()) {
                $businessCode = $baseCode . '-' . $suffix;
                $suffix++;
            }

            $tenant = Tenant::create([
                'name' => $request->business_name,
                'business_code' => $businessCode,
                'status' => 'active',
            ]);

            $outlet = Outlet::create([
                'tenant_id' => $tenant->id,
                'name' => $request->outlet_name ?: 'Outlet Utama',
                'code' => 'OUTLET01',
                'is_active' => true,
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'owner',
            ]);

            $plan = Plan::firstOrCreate(
                ['code' => 'starter-trial'],
                [
                    'name' => 'Starter Trial',
                    'price' => 0,
                    'max_outlets' => 3,
                    'max_users' => 10,
                    'max_products' => 1000,
                    'features' => [
                        'multi_outlet' => true,
                        'billing_access_owner_only' => true,
                        'mobile_access' => true,
                    ],
                ]
            );

            Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => 'trial',
                'starts_at' => now(),
                'trial_ends_at' => now()->addDays(30),
            ]);

            $token = $user->createToken('mobile_token')->plainTextToken;

            return $this->buildAuthPayload($user->fresh(['tenant']), $token);
        });

        return response()->json($payload, 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $user = User::with(['tenant', 'outlet'])
            ->where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        if ($user->tenant) {
            $this->tenantPlanGate->assertSubscriptionIsOperational($user->tenant);
        }

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json($this->buildAuthPayload($user, $token));
    }

    public function loginx(Request $request)
    {
        return $this->login($request);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me(Request $request)
    {
        $user = $this->currentUser($request)->loadMissing(['tenant', 'outlet']);

        return response()->json($this->buildAuthPayload($user));
    }

    private function buildAuthPayload(User $user, ?string $token = null): array
    {
        $accessibleOutlets = $this->accessibleOutlets($user);
        $currentOutlet = $user->role === 'kasir'
            ? $accessibleOutlets->first()
            : null;
        $subscription = $this->currentSubscription($user);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'outlet_id' => $user->outlet_id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'tenant' => $user->tenant ? [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
                'business_code' => $user->tenant->business_code,
                'status' => $user->tenant->status,
            ] : null,
            'accessible_outlets' => $accessibleOutlets->map(fn (Outlet $outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'code' => $outlet->code,
                'address' => $outlet->address,
                'phone' => $outlet->phone,
            ])->values()->all(),
            'current_outlet' => $currentOutlet ? [
                'id' => $currentOutlet->id,
                'name' => $currentOutlet->name,
                'code' => $currentOutlet->code,
                'address' => $currentOutlet->address,
                'phone' => $currentOutlet->phone,
            ] : null,
            'subscription' => $subscription ? [
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
            'permissions' => $this->permissionsFor($user),
        ];
    }
}
