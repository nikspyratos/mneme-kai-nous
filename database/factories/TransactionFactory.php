<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $expenses = Expense::all();
        $budgets = Budget::query()->with('tallies')->get();

        $selectedBudget = $budgets->random();
        $selectedTally = $selectedBudget->currentTally();
        return [
            'expense_id' => $this->faker->randomElement([$expenses->random(), null]),
            'budget_id' => $selectedBudget->id,
            'tally_id' => $selectedTally->id,
            'date' => Carbon::today()->startOfMonth()->addDays(random_int(0, Carbon::today()->endOfMonth()->day)),
            'type' => null, //TODO transaction categories
            'description' => $this->faker->words(5, true),
            'detail' => $this->faker->words(5, true),
            'amount' => random_int(1, 10000_00) * 100,
            'fee' => $this->faker->randomElement([random_int(0, 50_00), null]),
            'listed_balance' => null,
        ];
    }

    public function forAccount(Account $account): Factory
    {
        return $this->state(function ($attributes) use ($account) {
            return [
                'account_id' => $account->id,
                'currency' => $account->currency,
            ];
        });
    }


}
