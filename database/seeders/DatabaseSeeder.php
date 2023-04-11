<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\ExpectedTransaction;
use App\Models\Tally;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    private array $budgetData = [
        [
            'name' => 'Groceries',
            'currency' => 'ZAR',
            'amount' => 5000_00,
            'period_type' => 'monthly',
        ],
        [
            'name' => 'Discretionary',
            'currency' => 'ZAR',
            'amount' => 10000_00,
            'period_type' => 'monthly',
        ],
    ];

    private array $expectedTransactionData = [
        [
            'name' => 'Rent',
            'description' => null,
            'group' => null, //TODO group enum
            'currency' => 'ZAR',
            'amount' => '13000',
            'due_period' => 'monthly', //NOTE enum?
            'due_day' => 1,
        ],
        [
            'name' => 'Bond',
            'description' => null,
            'group' => null, //TODO group enum
            'currency' => 'ZAR',
            'amount' => '10500',
            'due_period' => 'monthly', //NOTE enum?
            'due_day' => 1,
        ],
        [
            'name' => 'Internet',
            'description' => null,
            'group' => null, //TODO group enum
            'currency' => 'ZAR',
            'amount' => '1000',
            'due_period' => 'monthly', //NOTE enum?
            'due_day' => 1,
        ],
        [
            'name' => 'Levies',
            'description' => null,
            'group' => null, //TODO group enum
            'currency' => 'ZAR',
            'amount' => '1000',
            'due_period' => null,
            'due_day' => null,
        ],
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate([
            'name' => 'Nik',
            'email' => config('app.admin_user_email'),
        ],
            [
                'password' => bcrypt('zzzzzzzz'),
            ]);
        foreach ($this->budgetData as $budgetDatum) {
            $budget = Budget::firstOrCreate($budgetDatum);
            Tally::firstOrCreate([
                'budget_id' => $budget->id,
                'name' => $budget->name,
                'currency' => $budget->currency,
                'balance' => 0,
                'start_date' => Carbon::today()->startOfMonth(),
                'end_date' => Carbon::today()->endOfMonth(),
            ]);
        }
        foreach ($this->expectedTransactionData as $expectedTransactionDatum) {
            ExpectedTransaction::firstOrCreate($expectedTransactionDatum);
        }
        $this->call(PerceptionSeeder::class);
        $this->call(LoadsheddingScheduleSeeder::class);
    }
}
