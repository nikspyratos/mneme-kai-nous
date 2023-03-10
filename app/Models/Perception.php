<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perception extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
