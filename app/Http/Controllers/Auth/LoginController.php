<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LoginChallenge;
use App\Mail\LoginChallengeMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /** ログイン画面表示 */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /** ログイン処理：未認証は初回認証へ／認証済みは毎回メール承認 */
    public function login(Request $request)
    {
        // 入力バリデーション
        $credentials = $request->validate(
            [
                'email'    => ['required','string','email'],
                'password' => ['required','string','min:8'],
            ],
            [
                'email.required'    => 'メールアドレスを入力してください',
                'email.email'       => 'メールアドレスはメール形式で入力して下さい',
                'password.required' => 'パスワードを入力してください',
                'password.min'      => 'パスワードは8文字以上で入力してください',
            ]
        );

        // ユーザー確認
        $user = User::where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。'])
                         ->onlyInput('email');
        }

        // ① アカウント自体が未認証 → 初回メール認証へ
        if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            try { $user->sendEmailVerificationNotification(); } catch (\Throwable $e) {}
            return redirect()->route('verification.notice')
                ->with('status', '認証メールを送信しました。メール内リンクから認証してください。');
        }

        // ② 認証済み → 毎回のログインチャレンジを発行
        // 既存の未使用チャレンジを無効化
        LoginChallenge::where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        // 新規チャレンジ
        $challenge = LoginChallenge::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'ip'         => (string) $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'expires_at' => now()->addMinutes(10),
        ]);

        // 送信
        Mail::to($user->email)->send(new LoginChallengeMail($challenge));

        // 再送用に user_id を保存（ここが大事：リダイレクト前に！）
        $request->session()->put('login.challenge_user_id', $user->id);

        // （任意）元ページを記録したい場合は保持
        // $request->session()->put('login.intended', url()->previous());

        // 案内ページへ（フラッシュ文言は付けない）
        return redirect()->route('login.challenge.sent');
    }

    /** ログアウト */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'ログアウトしました。');
    }
}
