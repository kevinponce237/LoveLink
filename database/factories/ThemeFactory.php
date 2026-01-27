<?php

namespace Database\Factories;

use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theme>
 */
class ThemeFactory extends Factory
{
    protected $model = Theme::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'primary_color' => fake()->hexColor(),
            'secondary_color' => fake()->hexColor(),
            'bg_color' => fake()->hexColor(),
            'bg_image_url' => null,
            'bg_image_media_id' => null,
            'css_class' => 'theme-' . fake()->word(),
        ];
    }

    /**
     * Indicate that the theme is a system theme.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    /**
     * Indicate that the theme has a background image.
     */
    public function withBackgroundImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'bg_image_url' => fake()->imageUrl(1920, 1080),
        ]);
    }

    /**
     * Indicate that the theme is for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
