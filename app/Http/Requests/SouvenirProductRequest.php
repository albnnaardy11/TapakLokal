<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SouvenirProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('vendor.access') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'in:Makanan khas,Kopi & minuman,Kain & kerajinan'],
            'region' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:10000'],
            'care' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'string', 'regex:#^/media/[0-9]+$#', 'max:2048'],
            'variants' => ['required', 'array', 'min:1', 'max:20'],
            'variants.*' => ['required', 'string', 'max:100', 'distinct'],
            'price' => ['required', 'integer', 'min:1000', 'max:100000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'weight' => ['required', 'integer', 'min:1', 'max:100000'],
            'preparation_days' => ['required', 'integer', 'min:0', 'max:60'],
            'availability' => ['required', 'in:Preorder,Ready stock'],
            'pickup_only' => ['required', 'boolean'],
            'pickup_address' => ['required', 'string', 'min:15', 'max:1000'],
            'delivery_rates' => ['nullable', 'array', 'max:100'],
            'delivery_rates.*' => ['array:service,region,fee'],
            'delivery_rates.*.service' => ['required', 'string', 'max:100'],
            'delivery_rates.*.region' => ['required', 'string', 'max:100'],
            'delivery_rates.*.fee' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }
}
