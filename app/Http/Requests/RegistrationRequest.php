<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class RegistrationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<\Closure> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! $validator->errors()->has('email') && User::whereRaw('lower(email) = ?', [$this->input('email')])->exists()) {
                $validator->errors()->add('email', 'Alamat email tidak tersedia.');
            }
        }];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'max:128', Password::min(10)->letters()->numbers()],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', 'string'],
        ];
    }
}
