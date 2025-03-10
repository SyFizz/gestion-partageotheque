<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Fields :
     * 'name',
     * 'identifier',
     * 'description',
     * 'category_id',
     * 'item_status_id',
     * 'caution_amount',
     * 'notes',
     * 'is_archived'
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $category = \App\Models\Category::all()->random();
        $status = \App\Models\ItemStatus::all()->random();

        return [
            'name' => $this->faker->word(),
            'identifier' => $category->slug . '-' . $this->faker->randomNumber(8),
            'description' => $this->faker->text,
            'category_id' => $category->id,
            'item_status_id' => $status->id,
            'caution_amount' => $this->faker->randomNumber(2),
            'notes' => $this->faker->text,
            'is_archived' => $this->faker->boolean,
        ];
    }
}
