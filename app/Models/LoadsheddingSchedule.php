<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
