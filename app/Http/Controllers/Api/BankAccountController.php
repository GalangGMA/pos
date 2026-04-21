<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ResolvesTenantContext;
use Illuminate\Http\Request;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    use ResolvesTenantContext;

    public function index(Request $request)
    {
        $tenant = $this->currentTenant($request);

        return BankAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get([
            'bank_name',
            'account_number',
            'account_holder',
            'note'
        ]);
    }
}
