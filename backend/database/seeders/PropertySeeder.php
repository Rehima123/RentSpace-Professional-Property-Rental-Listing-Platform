<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('role', 'owner')->get();

        if ($owners->isEmpty()) {
            return;
        }

        $amenities = Amenity::pluck('id')->toArray();

        $properties = [
            [
                'title' => 'Modern 3-Bedroom Apartment in Bole',
                'description' => 'A beautifully furnished 3-bedroom apartment located in the heart of Bole, Addis Ababa. The apartment features modern finishes, floor-to-ceiling windows, and stunning city views. Walking distance to Bole Atlas Hotel, restaurants, and international schools.',
                'type' => 'apartment',
                'price' => 35000,
                'city' => 'Addis Ababa',
                'sub_city' => 'Bole',
                'address' => 'Bole Atlas, Near Edna Mall',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'kitchens' => 1,
                'furnishing_status' => 'furnished',
                'size_sqm' => 120.00,
                'available_date' => now()->addDays(7)->format('Y-m-d'),
                'status' => 'approved',
                'is_featured' => true,
            ],
            [
                'title' => 'Spacious Studio in Sarbet',
                'description' => 'A compact yet comfortable studio apartment in Sarbet sub-city. Perfect for single professionals or students. Comes with a kitchenette and built-in wardrobe. Close to public transport.',
                'type' => 'studio',
                'price' => 8500,
                'city' => 'Addis Ababa',
                'sub_city' => 'Sarbet',
                'address' => 'Sarbet, Around Mekanisa Road',
                'bedrooms' => 0,
                'bathrooms' => 1,
                'kitchens' => 1,
                'furnishing_status' => 'semi-furnished',
                'size_sqm' => 35.00,
                'available_date' => now()->format('Y-m-d'),
                'status' => 'approved',
                'is_featured' => false,
            ],
            [
                'title' => '4-Bedroom House with Garden in CMC',
                'description' => 'A spacious 4-bedroom villa-style house with a large garden and private parking. Ideal for families. Located in a quiet neighborhood in CMC area, close to international schools.',
                'type' => 'house',
                'price' => 65000,
                'city' => 'Addis Ababa',
                'sub_city' => 'Yeka',
                'address' => 'CMC Road, Around CMC Michael Church',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'kitchens' => 1,
                'furnishing_status' => 'unfurnished',
                'size_sqm' => 280.00,
                'available_date' => now()->addDays(14)->format('Y-m-d'),
                'status' => 'approved',
                'is_featured' => true,
            ],
            [
                'title' => '2-Bedroom Condominium in Ayat',
                'description' => 'Government condominium with 2 bedrooms and a living room. Recently renovated with new tiles and paint. Elevator access. Utilities included.',
                'type' => 'condominium',
                'price' => 12000,
                'city' => 'Addis Ababa',
                'sub_city' => 'Kolfe Keranio',
                'address' => 'Ayat Condominium Block 7',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'kitchens' => 1,
                'furnishing_status' => 'unfurnished',
                'size_sqm' => 68.00,
                'available_date' => now()->addDays(3)->format('Y-m-d'),
                'status' => 'approved',
                'is_featured' => false,
            ],
            [
                'title' => 'Modern Office Space in Kazanchis',
                'description' => 'A fully fitted office space with 5 rooms, a reception area, and a conference room. 24/7 generator backup, fiber internet, and underground parking. Suitable for NGOs, embassies, and corporate tenants.',
                'type' => 'office',
                'price' => 95000,
                'city' => 'Addis Ababa',
                'sub_city' => 'Kirkos',
                'address' => 'Kazanchis, Behind Sheraton Addis',
                'bedrooms' => 0,
                'bathrooms' => 2,
                'kitchens' => 0,
                'furnishing_status' => 'furnished',
                'size_sqm' => 200.00,
                'available_date' => now()->format('Y-m-d'),
                'status' => 'approved',
                'is_featured' => true,
            ],
            [
                'title' => '2-Bedroom Apartment in Bahir Dar',
                'description' => 'A well-maintained apartment near Lake Tana in Bahir Dar. The apartment has two bedrooms, a dining area, and a small balcony with lake views. Close to the university and commercial area.',
                'type' => 'apartment',
                'price' => 9500,
                'city' => 'Bahir Dar',
                'sub_city' => 'Kebele 10',
                'address' => 'Near Bahir Dar University, Main Campus',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'kitchens' => 1,
                'furnishing_status' => 'semi-furnished',
                'size_sqm' => 75.00,
                'available_date' => now()->addDays(5)->format('Y-m-d'),
                'status' => 'approved',
                'is_featured' => false,
            ],
            [
                'title' => 'Commercial Property in Dessie',
                'description' => 'Ground floor commercial space suitable for a shop, pharmacy, or office in a busy area of Dessie. High foot traffic location near the main market.',
                'type' => 'commercial',
                'price' => 18000,
                'city' => 'Dessie',
                'sub_city' => 'Kebele 05',
                'address' => 'Dessie Main Market Area',
                'bedrooms' => 0,
                'bathrooms' => 1,
                'kitchens' => 0,
                'furnishing_status' => 'unfurnished',
                'size_sqm' => 60.00,
                'available_date' => now()->format('Y-m-d'),
                'status' => 'approved',
                'is_featured' => false,
            ],
            [
                'title' => '3-Bedroom House in Hawassa',
                'description' => 'A charming 3-bedroom bungalow-style house with a small garden in Hawassa. Near Lake Hawassa and the main business district. Quiet neighborhood, ideal for families.',
                'type' => 'house',
                'price' => 22000,
                'city' => 'Hawassa',
                'sub_city' => 'Tabor',
                'address' => 'Tabor Sub-city, Near Hawassa University',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'kitchens' => 1,
                'furnishing_status' => 'unfurnished',
                'size_sqm' => 150.00,
                'available_date' => now()->addDays(10)->format('Y-m-d'),
                'status' => 'pending',
                'is_featured' => false,
            ],
        ];

        foreach ($properties as $i => $data) {
            $owner = $owners[$i % $owners->count()];

            $slug = Str::slug($data['title']) . '-' . Str::lower(Str::random(6));

            $property = Property::firstOrCreate(
                ['slug' => $slug],
                array_merge($data, ['owner_id' => $owner->id, 'slug' => $slug, 'currency' => 'ETB'])
            );

            // Attach 3–6 random amenities
            $randomAmenities = collect($amenities)->shuffle()->take(rand(3, 6));
            $property->amenities()->sync($randomAmenities);
        }
    }
}
