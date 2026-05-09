<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'category_id' => Category::query()->inRandomOrder()->value('id') ?? 1,
            'uploaded_by' => User::query()->inRandomOrder()->value('id'),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'description' => fake()->paragraph(),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'published_year' => fake()->numberBetween(2000, (int) date('Y')),
            'isbn' => fake()->bothify('978##########'),
            'language' => fake()->randomElement(['vi', 'en']),
            'pages' => fake()->numberBetween(50, 600),
            'file_url' => 'https://res.cloudinary.com/demo/raw/upload/v1/sample.pdf',
            'cover_image' => 'https://res.cloudinary.com/demo/image/upload/v1/sample.jpg',
            'view_count' => fake()->numberBetween(50, 2000),
            'download_count' => fake()->numberBetween(0, 500),
            'is_featured' => false,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
