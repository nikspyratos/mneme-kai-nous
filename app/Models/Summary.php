<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionCategories;
use App\Enums\TransactionTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Summary
 *
 * @property int $id
 * @property string $name
 * @property array $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Summary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary query()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 * @mixin IdeHelperSummary
 */
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
        $data = Transaction::selectRaw('id, currency, category, SUM(amount) as total')
            ->where('type', TransactionTypes::DEBIT->value)
            ->whereIn('category', $categories)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category')
            ->get()
            ->toArray();
        $tallies = Tally::forPeriod($startDate, $endDate)->get()->toArray();
        foreach ($tallies as $tally) {
            $data[$tally->name] = $tally->formatted_balance . ' / ' . $tally->formatted_limit . ' - ' . $tally->getBalancePercentageOfBudget() . '%';
        }

        return self::create([
            'name' => sprintf('Summary: %s - %s', $startDate->toDateString(), $endDate->toDateString()),
            'data' => $data,
        ]);
    }
}
