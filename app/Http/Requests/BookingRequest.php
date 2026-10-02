<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'participants' => ['required', 'integer', 'min:1', 'max:50'],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'idempotency_key' => ['required', 'uuid'],
            'promotion_code' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['required_if:checkout_flow,true', 'nullable', 'email', 'max:255'],
            'checkout_flow' => ['nullable', 'boolean'],
            'special_request' => ['nullable', 'string', 'max:2000'],
            'traveler_details' => ['required_if:checkout_flow,true', 'nullable', 'array', 'size:'.(int) $this->input('participants', 1)],
            'traveler_details.*' => ['array:name,profile_id'],
            'traveler_details.*.name' => ['required', 'string', 'max:100'],
            'traveler_details.*.profile_id' => ['nullable', 'integer', Rule::exists('traveler_profiles', 'id')->where('user_id', $this->user()?->id)],
        ];
    }
}
