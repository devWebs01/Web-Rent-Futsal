<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_owner' => $this->faker->name(),
            'account_number' => $this->faker->creditCardNumber(),
            'bank_name' => $this->faker->creditCardType(),
        ];
    }
}
