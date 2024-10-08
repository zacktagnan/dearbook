<?php

namespace App\Models;

use App\Traits\Attachmentable;
use App\Traits\CustomDateFormatting;
use App\Traits\Reactionable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelArchivable\Archivable;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Attachmentable;
    use Reactionable;
    use CustomDateFormatting;
    use Archivable;

    protected $fillable = [
        'body',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function currentUserComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function latestComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public static function listedOnTimeLine($userId): Builder
    {
        return Post::withCount(['reactions',])
            ->with([
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'currentUserComments' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'latestComments',
                'comments',
            ])
            ->latest();
    }
}
