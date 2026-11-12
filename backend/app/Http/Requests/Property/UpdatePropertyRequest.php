<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['sometimes', 'required', 'string', 'max:255'],
            'description'      => ['sometimes', 'required', 'string', 'min:50'],
            'type'             => ['sometimes', 'required', Rule::in(['apartment','house','studio','condominium','commercial','office'])],
            'price'            => ['sometimes', 'required', 'numeric', 'min:0'],
            'city'             => ['sometimes', 'required', 'string', 'max:100'],
            'sub_city'         => ['nullable', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:255'],
            'bedrooms'         => ['sometimes', 'required', 'integer', 'min:0', 'max:20'],
            'bathrooms'        => ['sometimes', 'required', 'integer', 'min:0', 'max:20'],
            'kitchens'         => ['sometimes', 'required', 'integer', 'min:0', 'max:10'],
            'furnishing_status'=> ['sometimes', 'required', Rule::in(['furnished','semi-furnished','unfurnished'])],
            'size_sqm'         => ['nullable', 'numeric', 'min:1'],
            'available_date'   => ['nullable', 'date'],
            'status'           => ['sometimes', Rule::in(['pending','approved','rejected','rented','unavailable'])],
            'amenity_ids'      => ['nullable', 'array'],
            'amenity_ids.*'    => ['integer', 'exists:amenities,id'],

            'images'           => ['nullable', 'array', 'max:10'],
            'images.*'         => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'primary_image_index' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
