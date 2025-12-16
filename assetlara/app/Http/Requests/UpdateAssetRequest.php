<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by route middleware and policy
    }

    public function rules(): array
    {
        // Get the asset ID from the route parameter
        $assetId = $this->route('asset')->id;

        return [
            // 🟢 CRITICAL: Ensure the serial number is unique, BUT ignore the current asset's own serial number.
            'serial_number' => ['required', 'string', 'max:255', 'unique:assets,serial_number,' . $assetId],

            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'status' => ['required', 'string', 'in:available,assigned,broken,maintenance'],

            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }
}