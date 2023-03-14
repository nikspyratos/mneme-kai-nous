<?php

namespace Database\Factories;

use App\Enums\AccountTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'test', //$this->faker->words(random_int(0, 3)),
            'bank_name' => $this->faker->randomElement(['Absa', 'Bank Zero', 'Investec', 'Tyme Bank']),
            'account_number' => '1234567890',
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP', 'ZAR']),
            'balance' => $this->faker->randomNumber(5) * 100,
            'type' => $this->faker->randomElement(AccountTypes::cases())->value,
        ];
    }
}
