<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * このリクエストを認可するかどうか。
     */
    public function authorize(): bool
    {
        return true; // 認証不要なログインなので true に
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    /**
     * 日本語のバリデーションメッセージ
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'メールアドレスを入力してください',
            'email.email'       => 'メールアドレスはメール形式で入力して下さい',
            'password.required' => 'パスワードを入力してください',
        ];
    }
}
