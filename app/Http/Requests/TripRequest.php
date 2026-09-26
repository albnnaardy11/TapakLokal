<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('vendor.access') ?? false;
    }

    public function rules(): array
    {
        $trip = $this->route('trip');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('trips')->ignore($trip?->id)],
            'type' => ['required', Rule::in(['open-trip', 'private-trip', 'open-po'])],
            'destination' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:10000'],
            'itinerary' => ['required', 'string', 'max:10000'],
            'meeting_point' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'capacity' => ['required', 'integer', 'between:1,10000'],
            'price' => ['required', 'integer', 'between:1000,100000000'],
            'experience' => ['nullable', 'array'],
            'status' => ['required', Rule::in(['draft', 'pending'])],
        ];
    }
}
