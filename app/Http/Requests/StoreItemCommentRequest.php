<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルートが auth ミドルウェア配下なので true でOK
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
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
        ];
    }
}
