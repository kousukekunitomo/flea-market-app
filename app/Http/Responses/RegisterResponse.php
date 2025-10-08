<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // 「新規登録からの認証完了後はプロフィールへ」のフラグを保存
        $request->session()->put('after_register', true);

        // まずは初回メール認証案内へ
        return redirect()->route('verification.notice');
    }
}
