<?php

declare(strict_types=1);

namespace App\Models;

use App\Enumerations\TransactionCategories;
use App\Enumerations\TransactionTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Summary extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'data',
    ];

    public $casts = [
        'data' => 'array',
    ];

    public static function createForPeriod(Carbon $startDate, Carbon $endDate): self
    {
        $categories = TransactionCategories::values();
        $data = [];
        $transactions = Transaction::selectRaw('id, currency, category, SUM(amount) as total')
            ->where('type', TransactionTypes::DEBIT->value)
            ->whereIn('category', $categories)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category')
            ->get();
        $tallies = Tally::forPeriod($startDate, $endDate)->get();

        foreach ($transactions as $transaction) {
            $data[$transaction->category] = $transaction->formatKeyAsMoneyString('total');
        }
        foreach ($tallies as $tally) {
            $data[$tally->name] = $tally->formatted_balance;
            $data[$tally->name . ' Percentage'] = $tally->getBalancePercentageOfBudget() . '%';
        }

        return self::create([
            'name' => sprintf('Summary: %s - %s', $startDate->format('M Y'), $endDate->format('M Y')),
            'data' => $data,
        ]);
    }
}
