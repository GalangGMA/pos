<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user?->role === 'kasir') {
            return redirect()->intended(route('kasir.index'));
        }

        return redirect()->intended(Filament::getUrl());
    }
}
