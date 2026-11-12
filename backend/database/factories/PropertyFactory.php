<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->randomElement([
            'Modern Apartment in Bole',
            'Spacious Villa in CMC',
            'Studio Near Sarbet',
            '3-Bedroom House in Gurd Shola',
            'Office Space in Kazanchis',
            'Condominium in Ayat',
            'Commercial Space in Merkato',
            'Furnished Apartment in Piassa',
        ]) . ' ' . fake()->numerify('##');

        $cities = ['Addis Ababa', 'Bahir Dar', 'Hawassa', 'Adama', 'Dessie', 'Kombolcha'];

        return [
            'owner_id'          => User::factory()->owner(),
            'title'             => $title,
            'slug'              => Str::slug($title) . '-' . Str::lower(Str::random(6)),
            'description'       => fake()->paragraphs(3, true),
            'type'              => fake()->randomElement(['apartment', 'house', 'studio', 'condominium', 'commercial', 'office']),
            'price'             => fake()->numberBetween(5000, 150000),
            'currency'          => 'ETB',
            'city'              => fake()->randomElement($cities),
            'sub_city'          => fake()->word(),
            'address'           => fake()->streetAddress(),
            'bedrooms'          => fake()->numberBetween(0, 5),
            'bathrooms'         => fake()->numberBetween(1, 4),
            'kitchens'          => fake()->numberBetween(0, 2),
            'furnishing_status' => fake()->randomElement(['furnished', 'semi-furnished', 'unfurnished']),
            'size_sqm'          => fake()->numberBetween(30, 400),
            'available_date'    => now()->addDays(fake()->numberBetween(0, 30))->format('Y-m-d'),
            'status'            => 'approved',
            'is_featured'       => false,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }

    public function featured(): static
    {
        return $this->state(['status' => 'approved', 'is_featured' => true]);
    }
}
