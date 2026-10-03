<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\PhoneNumberService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => PhoneNumberService::normalize(trim($this->input('phone'))) ?? $this->input('phone')]);
        }
    }

    /** @return array<string, mixed> */
    public function credentials(): array
    {
        if ($this->filled('phone')) {
            $users = PhoneNumberService::users($this->validated('phone'))->limit(2)->pluck('id');
            if ($users->count() !== 1) {
                throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak sesuai.']);
            }

            return ['id' => $users->first(), 'password' => $this->validated('password'), 'status' => 'active'];
        }

        $users = User::whereRaw('lower(email) = ?', [$this->validated('email')])->limit(2)->pluck('id');
        if ($users->count() !== 1) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak sesuai.']);
        }

        return ['id' => $users->first(), 'password' => $this->validated('password'), 'status' => 'active'];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required_without:phone', 'prohibits:phone', 'nullable', 'email', 'max:255'],
            'phone' => ['required_without:email', 'prohibits:email', 'nullable', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/D'],
            'password' => ['required', 'string', 'max:128'],
            'remember' => ['boolean'],
            'portal' => ['nullable', 'string', 'in:traveler,staff,all'],
        ];
    }
}
