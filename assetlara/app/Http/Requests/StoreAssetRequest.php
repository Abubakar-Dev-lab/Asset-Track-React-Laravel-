<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by route middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 🟢 CRITICAL: This ensures no two laptops have the same SN.
            'serial_number' => ['required', 'string', 'max:255', 'unique:assets,serial_number'],

            'name' => ['required', 'string', 'max:255'],
            // Ensures the category_id exists in the 'categories' table
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'status' => ['required', 'string', 'in:available,assigned,broken,maintenance'],

            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }
}
