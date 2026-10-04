<?php

namespace App\Http\Requests;

use App\Services\TripRichText;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('vendor.access') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $experience = $this->input('experience');
        if (is_array($experience)) {
            foreach (['description_html' => 'description', 'itinerary_html' => 'itinerary'] as $key => $field) {
                if (isset($experience[$key]) && is_string($experience[$key])) {
                    $experience[$key] = TripRichText::sanitize($experience[$key]);
                    $this->merge([$field => TripRichText::plain($experience[$key])]);
                }
            }
            $this->merge(['experience' => $experience]);
            if (! empty($experience['detail']['images'][0]) && is_string($experience['detail']['images'][0])) {
                $this->merge(['image_url' => $experience['detail']['images'][0]]);
            }
        }
        if (blank($this->slug) && filled($this->title)) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        } elseif (filled($this->slug)) {
            $this->merge([
                'slug' => Str::slug($this->slug),
            ]);
        }

        if ($this->has('price') && is_string($this->price)) {
            $this->merge([
                'price' => (int) preg_replace('/\D/', '', (string) $this->price),
            ]);
        }

        if ($this->has('image_url') && filled($this->image_url)) {
            if (str_starts_with($this->image_url, '/')) {
                $this->merge(['image_url' => url($this->image_url)]);
            }
        }
    }

    public function rules(): array
    {
        $trip = $this->route('trip');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('trips')->ignore($trip?->id)],
            'type' => ['required', Rule::in(['open-trip', 'private-trip'])],
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
            'experience.description_html' => ['nullable', 'string', 'max:30000'],
            'experience.itinerary_html' => ['nullable', 'string', 'max:30000'],
            'experience.detail' => ['nullable', 'array'],
            'experience.detail.subtitle' => ['nullable', 'string', 'max:350'],
            'experience.detail.meetingTime' => ['nullable', 'string', 'max:100'],
            'experience.detail.arrivalNote' => ['nullable', 'string', 'max:180'],
            'experience.detail.meetingNote' => ['nullable', 'string', 'max:2000'],
            'experience.detail.coordinates' => ['nullable', 'string', 'max:100', 'regex:/^-?\d{1,2}(?:\.\d+)?,\s*-?\d{1,3}(?:\.\d+)?$/'],
            'experience.detail.images' => [$this->input('status') === 'pending' ? 'required' : 'nullable', 'array', $this->input('status') === 'pending' ? 'size:5' : 'max:5'],
            'experience.detail.images.*' => ['required', 'string', 'distinct', 'url:http,https', 'max:2048'],
            'experience.highlights' => ['nullable', 'array', 'max:20'],
            'experience.highlights.*.title' => ['required', 'string', 'max:180'],
            'experience.highlights.*.description' => ['nullable', 'string', 'max:3000'],
            'experience.highlights.*.time' => ['nullable', 'string', 'max:100'],
            'experience.highlights.*.location' => ['nullable', 'string', 'max:180'],
            'experience.highlights.*.image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'experience.highlights.*.images' => ['nullable', 'array', 'max:5'],
            'experience.highlights.*.images.*' => ['required', 'url:http,https', 'max:2048'],
            'experience.highlights.*.icon' => ['nullable', 'in:Compass,MapPin,BedDouble,Utensils,Camera,ShieldCheck,Users,Star,CalendarDays'],
            'experience.destinations' => ['nullable', 'array', 'max:30'],
            'experience.destinations.*.name' => ['required', 'string', 'max:180'],
            'experience.destinations.*.activity' => ['nullable', 'string', 'max:3000'],
            'experience.destinations.*.time' => ['nullable', 'string', 'max:100'],
            'experience.destinations.*.note' => ['nullable', 'string', 'max:3000'],
            'experience.destinations.*.image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'experience.destinations.*.subtitle' => ['nullable', 'string', 'max:300'],
            'experience.destinations.*.coordinates' => ['nullable', 'string', 'max:100', 'regex:/^-?\d{1,2}(?:\.\d+)?,\s*-?\d{1,3}(?:\.\d+)?$/'],
            'experience.itineraryDays' => ['nullable', 'array', 'max:60'],
            'experience.itineraryDays.*.day' => ['required', 'string', 'max:100'],
            'experience.itineraryDays.*.meals' => ['nullable', 'string', 'max:180'],
            'experience.itineraryDays.*.activities' => ['required', 'array', 'min:1', 'max:50'],
            'experience.itineraryDays.*.activities.*' => ['required', 'string', 'max:2000'],
            'experience.facilityDetails' => ['nullable', 'array', 'max:50'],
            'experience.facilityDetails.*.title' => ['required', 'string', 'max:180'],
            'experience.facilityDetails.*.category' => ['nullable', 'string', 'max:100'],
            'experience.facilityDetails.*.note' => ['nullable', 'string', 'max:2000'],
            'experience.facilities' => ['nullable', 'array', 'max:12'],
            'experience.facilities.*.label' => ['required', 'string', 'max:100'],
            'experience.facilities.*.icon' => ['required', 'in:Compass,MapPin,BedDouble,Utensils,Camera,ShieldCheck,Users,Star,CalendarDays'],
            'experience.included' => ['nullable', 'array', 'max:50'],
            'experience.included.*' => ['required', 'string', 'max:500'],
            'experience.excluded' => ['nullable', 'array', 'max:50'],
            'experience.excluded.*' => ['required', 'string', 'max:500'],
            'experience.packingItems' => ['nullable', 'array', 'max:50'],
            'experience.packingItems.*' => ['required', 'string', 'max:500'],
            'experience.faqs' => ['nullable', 'array', 'max:30'],
            'experience.faqs.*.question' => ['required', 'string', 'max:300'],
            'experience.faqs.*.answer' => ['required', 'string', 'max:3000'],
            'experience.panoramas' => ['nullable', 'array', 'max:10'],
            'experience.panoramas.*.title' => ['required', 'string', 'max:180'],
            'experience.panoramas.*.label' => ['nullable', 'string', 'max:180'],
            'experience.panoramas.*.image_url' => ['required', 'url:http,https', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'pending'])],
        ];
    }

    public function messages(): array
    {
        return [
            'experience.detail.images.required' => 'Unggah lima foto perjalanan sebelum mengirim ke admin.',
            'experience.detail.images.size' => 'Galeri harus berisi tepat lima foto perjalanan.',
            'experience.detail.images.*.distinct' => 'Gunakan lima foto yang berbeda.',
        ];
    }
}
