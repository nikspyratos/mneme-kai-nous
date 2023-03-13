<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    public $fillable = [
        'perception_id',
        'content',
        'author',
    ];

    public function scopeShortContent($query)
    {
        return $query->whereRaw('length(content) < 200');
    }

    public function perception(): BelongsTo
    {
        return $this->belongsTo(Perception::class);
    }
}
