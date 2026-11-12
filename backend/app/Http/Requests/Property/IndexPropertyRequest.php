<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexPropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'            => ['nullable', 'string', 'max:255'],
            'city'              => ['nullable', 'string', 'max:100'],
            'type'              => ['nullable', Rule::in(['apartment','house','studio','condominium','commercial','office'])],
            'min_price'         => ['nullable', 'numeric', 'min:0'],
            'max_price'         => ['nullable', 'numeric', 'min:0'],
            'bedrooms'          => ['nullable', 'integer', 'min:0'],
            'bathrooms'         => ['nullable', 'integer', 'min:0'],
            'furnishing_status' => ['nullable', Rule::in(['furnished','semi-furnished','unfurnished'])],
            'available_only'    => ['nullable', 'boolean'],
            'sort'              => ['nullable', Rule::in(['newest','price_asc','price_desc'])],
            'per_page'          => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
