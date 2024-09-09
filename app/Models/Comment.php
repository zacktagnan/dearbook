<?php

namespace App\Models;

use App\Traits\Attachmentable;
use App\Traits\CustomDateFormatting;
use App\Traits\Reactionable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;
    use Attachmentable;
    use Reactionable;
    use CustomDateFormatting;

    public array $childCommentsArr = [];
    public int $totalOfComments = 0;
    public int $totalOfDirectChildComments = 0;

    protected $fillable = [
        'parent_id',
        'post_id',
        'comment',
        'user_id',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function childComments(): HasMany
    {
        // return $this->hasMany(Comment::class, 'parent_id');
        // o, tratándose de la misma clase...
        return $this->hasMany(self::class, 'parent_id');
    }

    public function latestChildComments(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeRoot(Builder $query)
    {
        $query->whereNull('parent_id');
    }
}
