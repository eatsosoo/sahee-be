<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(20),
            'author' => fake()->name(),
            'description' => fake()->text(200),
            'price' => rand(10000, 1000000),
            'stock' => rand(10, 10),
            'book_cover_url' => 'https://intamphuc.vn/wp-content/uploads/2023/06/mau-bia-sach-dep-2.jpg',
            'user_id' => rand(1, 10),
            'category_id' => rand(1, 6),
        ];
    }
}
