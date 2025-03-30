<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号はハイフンありの8文字で入力してください。',
            'address.required' => '住所を入力してください。',
            'building.required' => '建物名を入力してください。',
        ];
    }
}
