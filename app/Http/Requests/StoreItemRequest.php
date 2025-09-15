<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'image'          => ['required','image','mimes:jpeg,png,jpg,webp','max:4096'],
            'category_ids'   => ['required','array','min:1'],
            'category_ids.*' => ['integer','exists:categories,id'],
            'condition_id'   => ['required','exists:conditions,id'],
            'name'           => ['required','string','max:255'],
            'brand'          => ['nullable','string','max:255'],
            'description'    => ['required','string','max:2000'],
            'price'          => ['required','integer','min:1','max:99999999'],
        ];
    }

    public function attributes(): array
    {
        return [
            'image'          => '商品画像',
            'category_ids'   => 'カテゴリー',
            'category_ids.*' => 'カテゴリー',
            'condition_id'   => '商品の状態',
            'name'           => '商品名',
            'brand'          => 'ブランド名',
            'description'    => '商品説明',
            'price'          => '価格',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => ':attributeを選択してください。',
            'image.image'    => ':attributeの形式が不正です。',
            'image.mimes'    => ':attributeはjpeg, png, webp形式で指定してください。',
            'image.max'      => ':attributeは最大4MBまでです。',
            'category_ids.required' => ':attributeを1つ以上選択してください。',
            'category_ids.min'      => ':attributeを1つ以上選択してください。',
            'condition_id.required' => ':attributeを選択してください。',
            'name.required'         => ':attributeを入力してください。',
            'description.required'  => ':attributeを入力してください。',
            'price.required'        => ':attributeを入力してください。',
            'price.integer'         => ':attributeは半角数字で入力してください（カンマ不可）。',
            'price.min'             => ':attributeは:min以上で入力してください。',
            'price.max'             => ':attributeは:max以下で入力してください。',
        ];
    }

    protected function prepareForValidation(): void
    {
        // "12,345" → 12345、全角→半角
        $price = (string) $this->input('price', '');
        $price = preg_replace('/[^\d]/u', '', mb_convert_kana($price, 'n', 'UTF-8'));
        $this->merge(['price' => $price === '' ? null : (int) $price]);
    }
}
