<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'profile_image.image' => 'プロフィール画像は画像ファイルを選択してください。',
            'profile_image.mimes' => 'プロフィール画像は .jpeg または .png のみ使用できます。',
            'profile_image.max' => 'プロフィール画像のサイズは2MB以下にしてください。',
        ];
    }
}
