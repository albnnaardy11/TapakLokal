<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreVendorApplicationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.required' => 'Alamat Gmail wajib diisi.',
            'email.regex' => 'Gunakan alamat Gmail dengan akhiran @gmail.com.',
            'email.email' => 'Masukkan alamat Gmail yang valid.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon harus berisi 8–15 digit, boleh diawali +.',
        ];
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'track' => ['required', Rule::in(['trip', 'souvenir'])],
            'business' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'regex:/^[^@]+@gmail[.]com$/i', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
            'phone' => ['required', 'string', 'regex:/^[+]?[0-9]{8,15}$/'],
            'notes' => ['nullable', 'string', 'max:1500'],
        ];
    }
}
