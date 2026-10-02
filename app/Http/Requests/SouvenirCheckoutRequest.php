<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SouvenirCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'idempotency_key' => ['required', 'uuid'],
            'contact_name' => ['required', 'string', 'max:150'],
            'contact_email' => ['required_if:checkout_flow,true', 'nullable', 'email', 'max:255'],
            'checkout_flow' => ['nullable', 'boolean'],
            'contact_phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'method' => ['required', 'in:pickup,delivery'],
            'pickup_date' => ['exclude_unless:method,pickup', 'required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->addMonths(3)->toDateString()],
            'address' => ['exclude_unless:method,delivery', 'required', 'string', 'min:15', 'max:1000'],
            'delivery_region' => ['exclude_unless:method,delivery', 'required', 'string', 'max:100'],
            'delivery_service' => ['exclude_unless:method,delivery', 'required', 'string', 'max:100'],
        ];
    }
}
