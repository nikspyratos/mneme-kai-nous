<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Quote
 *
 * @property int $id
 * @property int $perception_id
 * @property string $content
 * @property string|null $author
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Perception|null $perception
 * @method static \Database\Factories\QuoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote shortContent()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote wherePerceptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
