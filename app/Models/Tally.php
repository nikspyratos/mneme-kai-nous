<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tally extends Model
{
    use HasFactory;

    public $fillable = [
        'budget_id',
        'name',
        'currency',
        'balance',
        'start_date',
        'end_date',
    ];

    public $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
