<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    public function index()
    {
        return BankAccount::where('is_active', true)->get([
            'bank_name',
            'account_number',
            'account_holder',
            'note'
        ]);
    }
}
