<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Perception
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quote> $quotes
 * @property-read int|null $quotes_count
 *
 * @method static \Database\Factories\PerceptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Perception newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Perception newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Perception query()
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
