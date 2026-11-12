<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Amenity>
 */
class AmenityFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'WiFi', 'Parking', 'Generator', 'Air Conditioning',
            'Water Tank', 'Security Guard', 'Garden', 'Elevator',
            'Swimming Pool', 'Gym', 'Balcony', 'Laundry',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
