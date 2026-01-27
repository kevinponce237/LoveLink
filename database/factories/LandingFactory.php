<?php

namespace Database\Factories;

use App\Models\Landing;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Landing>
 */
class LandingFactory extends Factory
{
    protected $model = Landing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $coupleNames = fake()->firstName() . ' & ' . fake()->firstName();
        
        return [
            'user_id' => User::factory(),
            'theme_id' => Theme::factory(),
            'slug' => Str::slug($coupleNames . '-' . fake()->randomNumber(3)),
            'couple_names' => $coupleNames,
            'anniversary_date' => fake()->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
            'bio_text' => fake()->paragraph(3),
        ];
    }

    /**
     * Crear una landing con nombres específicos de pareja
     */
    public function withCoupleNames(string $names): static
    {
        return $this->state(fn (array $attributes) => [
            'couple_names' => $names,
            'slug' => Str::slug($names . '-' . fake()->randomNumber(3)),
        ]);
    }

    /**
     * Crear una landing con slug específico
     */
    public function withSlug(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => $slug,
        ]);
    }

    /**
     * Crear una landing con una fecha de aniversario reciente
     */
    public function recentAnniversary(): static
    {
        return $this->state(fn (array $attributes) => [
            'anniversary_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * Crear una landing con fecha de aniversario en el futuro
     */
    public function futureAnniversary(): static
    {
        return $this->state(fn (array $attributes) => [
            'anniversary_date' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Crear una landing con bio extendida
     */
    public function withExtendedBio(): static
    {
        return $this->state(fn (array $attributes) => [
            'bio_text' => fake()->paragraphs(5, true),
        ]);
    }

    /**
     * Crear una landing sin bio
     */
    public function withoutBio(): static
    {
        return $this->state(fn (array $attributes) => [
            'bio_text' => null,
        ]);
    }

    /**
     * Crear una landing para un usuario específico
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Crear una landing con un tema específico
     */
    public function withTheme(Theme $theme): static
    {
        return $this->state(fn (array $attributes) => [
            'theme_id' => $theme->id,
        ]);
    }
}