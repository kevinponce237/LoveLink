<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear temas del sistema (user_id = null)
        Theme::create([
            'name' => 'Elegante Clásico',
            'description' => 'Tema elegante con tonos dorados y blancos clásicos',
            'primary_color' => '#FFD700',
            'secondary_color' => '#FFFFFF',
            'bg_color' => '#F5F5F5',
            'css_class' => 'theme-elegant-classic',
            'user_id' => null,
        ]);

        Theme::create([
            'name' => 'Romance Rosa',
            'description' => 'Tema romántico con tonos rosados suaves',
            'primary_color' => '#FF69B4',
            'secondary_color' => '#FFC0CB',
            'bg_color' => '#FFF0F5',
            'css_class' => 'theme-romantic-pink',
            'user_id' => null,
        ]);

        Theme::create([
            'name' => 'Natureza Verde',
            'description' => 'Tema natural con tonos verdes frescos',
            'primary_color' => '#228B22',
            'secondary_color' => '#90EE90',
            'bg_color' => '#F0FFF0',
            'css_class' => 'theme-nature-green',
            'user_id' => null,
        ]);

        Theme::create([
            'name' => 'Océano Azul',
            'description' => 'Tema sereno con tonos azules oceánicos',
            'primary_color' => '#4169E1',
            'secondary_color' => '#87CEEB',
            'bg_color' => '#F0F8FF',
            'css_class' => 'theme-ocean-blue',
            'user_id' => null,
        ]);

        Theme::create([
            'name' => 'Atardecer Cálido',
            'description' => 'Tema cálido con tonos naranjas y dorados',
            'primary_color' => '#FF8C00',
            'secondary_color' => '#FFE4B5',
            'bg_color' => '#FFF8DC',
            'css_class' => 'theme-warm-sunset',
            'user_id' => null,
        ]);
    }
}
