<?php

namespace Database\Factories;

use App\Models\Artikel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Artikel>
 */
class ArtikelFactory extends Factory
{
    protected $model = Artikel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul' => fake()->sentence(6),
            'konten' => fake()->paragraphs(4, true),
            'status' => Artikel::STATUS_PENDING,
            'is_draft' => true,
            'review_note' => null,
            'submitted_at' => null,
            'approved_at' => null,
            'published_at' => null,
        ];
    }
}
