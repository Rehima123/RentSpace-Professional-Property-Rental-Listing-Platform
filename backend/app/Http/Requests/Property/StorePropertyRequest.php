<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string', 'min:50'],
            'type'             => ['required', Rule::in(['apartment','house','studio','condominium','commercial','office'])],
            'price'            => ['required', 'numeric', 'min:0'],
            'city'             => ['required', 'string', 'max:100'],
            'sub_city'         => ['nullable', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:255'],
            'bedrooms'         => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms'        => ['required', 'integer', 'min:0', 'max:20'],
            'kitchens'         => ['required', 'integer', 'min:0', 'max:10'],
            'furnishing_status'=> ['required', Rule::in(['furnished','semi-furnished','unfurnished'])],
            'size_sqm'         => ['nullable', 'numeric', 'min:1'],
            'available_date'   => ['nullable', 'date', 'after_or_equal:today'],
            'amenity_ids'      => ['nullable', 'array'],
            'amenity_ids.*'    => ['integer', 'exists:amenities,id'],

            // Images: array of files
            'images'           => ['nullable', 'array', 'max:10'],
            'images.*'         => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5MB each
            'primary_image_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.min'  => 'Description must be at least 50 characters.',
            'images.*.image'   => 'Each file must be a valid image.',
            'images.*.mimes'   => 'Images must be jpeg, jpg, png, or webp.',
            'images.*.max'     => 'Each image must be under 5 MB.',
            'images.max'       => 'You may upload a maximum of 10 images.',
        ];
    }
}
