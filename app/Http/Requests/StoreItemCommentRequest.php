<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Fortifyの他ページと同様：デフォルトバッグで検証
            'content' => ['required', 'min:1', 'max:255'],
            // 空白のみを弾くなら ↓ を追加（任意）
            // 'content' => ['required', 'min:1', 'max:255', 'not_regex:/^\s*$/u'],
        ];
    }

    public function attributes(): array
    {
        return ['content' => 'コメント'];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'コメントを入力してください。',
            'content.min'      => 'コメントを入力してください。',
            'content.max'      => 'コメントは255文字以内で入力してください。',
        ];
    }

    // 失敗時はコメント欄へ戻す（Fortifyの見せ方に合わせつつUX向上）
    protected function getRedirectUrl(): string
    {
        return url()->previous() . '#comments';
    }
}
