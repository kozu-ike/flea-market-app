<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'condition' => 'required|in:新品・未使用,未使用に近い,目立った傷や汚れなし,やや傷や汚れあり,傷や汚れあり,全体的に状態が悪い',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
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
            'category_ids.required' => '商品のカテゴリーを選択してください。',
            'category_ids.array' => 'カテゴリーは複数選択可能です。',
            'category_ids.*.exists' => '選択したカテゴリーの中に無効なカテゴリーがあります。',
            'condition.required' => '商品の状態を選択してください。',
            'condition.in' => '商品の状態が無効です。',
            'price.required' => '商品価格を入力してください。',
            'price.numeric' => '商品価格は数値で入力してください。',
            'price.min' => '商品価格は0円以上で入力してください。',
            'stock.integer' => 'ストックは整数で入力してください。',
            'stock.min' => 'ストックは0以上の値にしてください。',
        ];
    }
}
