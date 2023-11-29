<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $brand = ['Avon', 'Boticario', 'Nike', 'Adidas', 'Lacoste', 'Rexona', 'Axe', 'Perdigao', 'Sadia'];

        return [
           'brand' => $brand[array_rand($brand)]
        ];
    }
}
