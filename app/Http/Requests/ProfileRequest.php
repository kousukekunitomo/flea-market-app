<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'name' => 'required|string|max:20',
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'profile_image.image' => '画像ファイルを指定してください。',
            'profile_image.mimes' => '画像ファイルはjpegまたはpng形式で指定してください。',
            'profile_image.max' => '画像ファイルのサイズは2MB以内で指定してください。',
            'name.required' => 'ユーザー名を入力してください。',
            'name.max' => 'ユーザー名は20文字以内で入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号はハイフンありの8桁（例: 123-4567）で入力してください。',
            'address.required' => '住所を入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',
            'building_name.max' => '建物名は255文字以内で入力してください。',
        ];
    }
}
