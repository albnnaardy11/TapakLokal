<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VirtualTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->hasPermission('content.manage') ?? false)
            && ($this->input('status') !== 'published' || $this->user()->hasPermission('content.publish'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('virtual_tours')->ignore($this->route('tour')?->id)],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'placement' => ['required', Rule::in(['homepage', 'trip', 'destination'])],
            'trip_id' => ['exclude_unless:placement,trip', 'required', 'integer', 'exists:trips,id'],
            'content_page_id' => ['exclude_unless:placement,destination', 'required', 'integer', Rule::exists('content_pages', 'id')->whereIn('type', ['destination', 'hidden-gem'])],
            'position' => ['required', 'integer', 'between:0,10000'],
            'file' => [$this->route('tour') ? 'nullable' : 'required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:25600'],
        ];
    }
}
