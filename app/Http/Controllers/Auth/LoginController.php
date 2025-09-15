<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /** ログインフォーム表示 */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /** ログイン処理 */
    public function login(LoginRequest $request)
    {
        // ★ フォーム(or クエリ)の intended をセッションへ
        //   → redirect()->intended() の参照先になる
        if ($request->filled('intended')) {
            $request->session()->put('url.intended', $request->input('intended'));
        }

        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // intended があればそこへ、なければマイリストへ
            return redirect()->intended(route('items.index', ['tab' => 'mylist']));
        }

        return back()
            ->withErrors(['email' => 'ログイン情報が登録されていません'])
            ->withInput();
    }

    /** ログアウト */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('items.index', ['tab' => 'recommend']);
    }
}
