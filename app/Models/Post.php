<?php

namespace App\Models;

use App\Traits\Reactionable;
use App\Traits\Attachmentable;
use LaravelArchivable\Archivable;
use App\Http\Enums\GroupUserStatus;
use App\Traits\CustomDateFormatting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Post
 *
 * @property Group $group
 */
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
        'preview',
        'user_id',
        'group_id',
    ];

    protected $casts = [
        'preview' => 'json',
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

    public static function listedOnTimeLine($userId, $orderByLatest = true): Builder
    {
        $query = Post::withCount(['reactions',])
            ->where(function ($query) {
                $query->whereHas('group', function ($query) {
                    $query->where('type', 'public')
                        ->whereNull('deleted_at');
                })->orWhere(function ($query) {
                    $query->whereHas('group', function ($query) {
                        $query->where('type', 'private')
                            ->whereNull('deleted_at');
                    })->whereHas('group.currentGroupUser', function ($query) {
                        $query->where('status', 'approved');
                    });
                })->orWhereDoesntHave('group');
            })
            ->with([
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'currentUserComments' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'latestComments',
                'comments',
            ]);

        if ($orderByLatest) {
            $query->latest();
        }

        return $query;
    }

    public static function scopeOnlyFromFollowers(Builder $query, int $userId): Builder
    {
        return $query->leftJoin('followers as f', function ($join) use ($userId) {
            $join->on('posts.user_id', '=', 'f.followed_id')
                ->where('f.follower_id', '=', $userId);
        })
            ->leftJoin('group_users as g_u', function ($join) use ($userId) {
                $join->on('g_u.group_id', '=', 'posts.group_id')
                    ->where('g_u.user_id', '=', $userId)
                    ->where('g_u.status', '=', GroupUserStatus::APPROVED->value);
            })
            ->whereNull('posts.deleted_at')
            ->where(function ($query) use ($userId) {
                $query->whereNotNull('f.follower_id')
                    ->orWhereNotNull('g_u.group_id')
                    // ->orWhere('posts.user_id', $userId)
                    // Para incluir los propios del AUTH
                ;
            })
            // Para no incluir los propios del AUTH
            ->whereNot('posts.user_id', $userId);
    }

    public static function detail($userId): Builder
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
                'group',
            ])
            ->withArchived()
            ->withTrashed();
    }

    public function isAuthor(int $userId): bool
    {
        return $this->user_id === $userId;
    }
}
