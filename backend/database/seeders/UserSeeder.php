<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@rentspace.et'],
            [
                'name' => 'RentSpace Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+251911000000',
                'status' => 'active',
            ]
        );

        // Sample owners
        $owners = [
            ['name' => 'Abebe Kebede', 'email' => 'owner1@rentspace.et', 'phone' => '+251911100001'],
            ['name' => 'Tigist Haile', 'email' => 'owner2@rentspace.et', 'phone' => '+251911100002'],
            ['name' => 'Solomon Tesfaye', 'email' => 'owner3@rentspace.et', 'phone' => '+251911100003'],
        ];

        foreach ($owners as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'phone' => $data['phone'],
                    'status' => 'active',
                ]
            );
        }

        // Sample tenants
        $tenants = [
            ['name' => 'Meron Alemu', 'email' => 'tenant1@rentspace.et', 'phone' => '+251922200001'],
            ['name' => 'Dawit Girma', 'email' => 'tenant2@rentspace.et', 'phone' => '+251922200002'],
            ['name' => 'Hana Bekele', 'email' => 'tenant3@rentspace.et', 'phone' => '+251922200003'],
        ];

        foreach ($tenants as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'tenant',
                    'phone' => $data['phone'],
                    'status' => 'active',
                ]
            );
        }
    }
}
