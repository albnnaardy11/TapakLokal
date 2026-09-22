<?php
namespace App\Http\Requests;

use App\Services\BackofficeRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BackofficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $module = app(BackofficeRegistry::class)->get($this->route('module'));
        return $this->user()?->hasPermission($module['permission'].'.manage') && count($module['fields']) > 0
            && (! $this->isMethod('post') || ($module['create'] ?? true));
    }

    public function rules(): array
    {
        $module = app(BackofficeRegistry::class)->get($this->route('module'));
        $rules = [];
        foreach ($module['fields'] as $name => $field) {
            $rules[$name] = [$field['required'] ? 'required' : 'nullable'];
            $rules[$name] = [...$rules[$name], ...match ($field['type']) {
                'number' => ['integer', 'min:0', 'max:1000000000'],
                'date', 'datetime-local' => ['date'],
                'url' => ['url:http,https', 'max:2048'],
                'select' => [Rule::in($field['options'])],
                'textarea' => ['string', 'max:50000'],
                default => ['string', 'max:255'],
            }];
        }
        $record = $this->route('record');
        foreach (['slug', 'code', 'key'] as $unique) {
            if (isset($rules[$unique])) {
                $rules[$unique][] = Rule::unique((new $module['model'])->getTable(), $unique)->ignore(is_string($record) && ctype_digit($record) ? (int) $record : null);
            }
        }
        if (isset($rules['slug'])) { $rules['slug'][] = 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'; }
        if (isset($rules['ends_at'])) { $rules['ends_at'][] = 'after_or_equal:starts_at'; }
        if (isset($rules['user_id'])) { $rules['user_id'][] = 'exists:users,id'; $rules['user_id'][] = Rule::unique('affiliates', 'user_id')->ignore($record); }
        if (isset($rules['commission_bps'])) { $rules['commission_bps'] = ['required', 'integer', 'between:0,10000']; }
        if ($this->route('module') === 'promotions' && $this->input('type') === 'percent') { $rules['value'] = ['required', 'integer', 'between:1,100']; }
        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) { $this->merge(['code' => strtoupper((string) $this->input('code'))]); }
    }
}

