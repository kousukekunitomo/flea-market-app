<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postal_code'    => ['required', 'regex:/^\d{3}-?\d{4}$/'], // 123-4567 or 1234567
            'address'        => ['required', 'string', 'max:255'],
            'building_name'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'    => '郵便番号は123-4567（ハイフンあり/なし可）の形式で入力してください',
            'address.required'     => '住所を入力してください',
            'address.max'          => '住所は:max文字以内で入力してください',
            'building_name.max'    => '建物名は:max文字以内で入力してください',
        ];
    }

    public function attributes(): array
    {
        return [
            'postal_code'   => '郵便番号',
            'address'       => '住所',
            'building_name' => '建物名',
        ];
    }
}
