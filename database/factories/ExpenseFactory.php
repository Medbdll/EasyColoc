<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 10, 500),
            'payer_id' => User::factory(),
            'colocation_id' => Colocation::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
