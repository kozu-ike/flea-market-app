<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'category_id' => 'required|exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'image.required' => '商品画像をアップロードしてください。',
            'image.image' => '商品画像は画像ファイルを選択してください。',
            'image.mimes' => '商品画像は .jpeg または .png のみ使用できます。',
            'category_id.required' => '商品のカテゴリーを選択してください。',
            'category_id.exists' => '選択したカテゴリーが無効です。',
            'condition_id.required' => '商品の状態を選択してください。',
            'condition_id.exists' => '選択した商品の状態が無効です。',
            'price.required' => '商品価格を入力してください。',
            'price.numeric' => '商品価格は数値で入力してください。',
            'price.min' => '商品価格は0円以上で入力してください。',
        ];
    }
}
