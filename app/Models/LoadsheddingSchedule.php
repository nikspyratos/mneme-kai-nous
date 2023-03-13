<?php

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
    ];

    public $casts = [
        'today_times' => 'array',
        'data' => 'json',
    ];

    public function getTodayTimesFormattedAttribute(): string
    {
        return Arr::join($this->today_times, ', ');
    }
}
