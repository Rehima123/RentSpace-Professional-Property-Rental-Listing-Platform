<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            'WiFi / Internet',
            'Air Conditioning',
            'Heating',
            'Parking',
            'Generator / Backup Power',
            'Water Tank',
            'Solar Power',
            'Security Guard',
            'CCTV / Security Camera',
            'Elevator / Lift',
            'Swimming Pool',
            'Gym / Fitness Center',
            'Balcony / Terrace',
            'Garden / Yard',
            'Laundry Room',
            'Dishwasher',
            'Refrigerator',
            'Washing Machine',
            'Microwave',
            'TV / Cable',
            'Pet Friendly',
            'Wheelchair Accessible',
            'Storage Room',
            'Shared Kitchen',
            '24/7 Water Supply',
        ];

        foreach ($amenities as $name) {
            Amenity::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
