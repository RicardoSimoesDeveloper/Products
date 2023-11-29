<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $type = Type::factory()->create();
        $brand = Brand::factory()->create();

        return [
            'type' => $type->id,
            'brand' => $brand->id,
            'description' => fake()->name(),
            'price' => rand(10, 1000),
            'stock' => rand(1, 2000)
        ];
    }
}
