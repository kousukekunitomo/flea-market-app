<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // ✅ redirect()->intended() ではなく、明示的に商品一覧へ
        return redirect()->route('items.index');
    }
}
