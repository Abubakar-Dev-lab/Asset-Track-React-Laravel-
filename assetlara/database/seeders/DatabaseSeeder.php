<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Employee Users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Laptops'],
            ['name' => 'Monitors'],
            ['name' => 'Keyboards'],
            ['name' => 'Mice'],
            ['name' => 'Phones'],
            ['name' => 'Tablets'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Sample Assets
        $laptopCategory = Category::where('name', 'Laptops')->first();
        $monitorCategory = Category::where('name', 'Monitors')->first();

        Asset::create([
            'category_id' => $laptopCategory->id,
            'name' => 'Dell Latitude 5420',
            'serial_number' => 'DL-LAT-001',
            'status' => 'available',
        ]);

        Asset::create([
            'category_id' => $laptopCategory->id,
            'name' => 'MacBook Pro 14"',
            'serial_number' => 'AP-MBP-001',
            'status' => 'available',
        ]);

        Asset::create([
            'category_id' => $monitorCategory->id,
            'name' => 'LG UltraWide 34"',
            'serial_number' => 'LG-UW-001',
            'status' => 'available',
        ]);
    }
}
