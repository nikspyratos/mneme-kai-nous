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

    public const DATING_RELATIONSHIPS = 1;
    public const AMBITION = 2;
    public const LEARNING = 3;
    public const LEADERSHIP_COLLABORATION = 4;
    public const FINANCE = 5;
    public const HEALTH = 6;
    public const HOBBIES = 7;
    public const MYSELF = 8;

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
