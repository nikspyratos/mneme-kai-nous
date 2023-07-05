<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\LoadsheddingSchedule
 *
 * @property int $id
 * @property string $name
 * @property string $zone
 * @property string $api_id
 * @property string $region
 * @property array|null $today_times
 * @property array|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_home
 * @property-read string $today_times_formatted
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereIsHome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereTodayTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereZone($value)
 *
 * @property bool $enabled
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereEnabled($value)
 *
 * @mixin \Eloquent
 */
class LoadsheddingSchedule extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'zone',
        'api_id',
        'region',
        'today_times',
        'data',
        'is_home',
        'enabled',
    ];

    public $casts = [
        'today_times' => 'array',
        'data' => 'json',
        'enabled' => 'bool',
    ];

    public function getTodayTimesFormattedAttribute(): string
    {
        return ! empty($this->today_times) ? Arr::join($this->today_times, ', ') : 'N/A';
    }
}
