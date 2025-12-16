<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true) . ' ' . fake()->randomElement(['Laptop', 'Monitor', 'Keyboard', 'Mouse', 'Tablet']),
            'serial_number' => strtoupper(fake()->unique()->bothify('SN-????-####')),
            'status' => fake()->randomElement(['available', 'assigned', 'broken', 'maintenance']),
            'image_path' => null,
        ];
    }

    /**
     * Indicate that the asset is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    /**
     * Indicate that the asset is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'assigned',
        ]);
    }

    /**
     * Indicate that the asset is broken.
     */
    public function broken(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'broken',
        ]);
    }

    /**
     * Indicate that the asset is in maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }
}
