<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;

    protected $fillable = [
        'name',
        'auto_approval',
        'type',
        'about',
        'user_id',
        'cover_path',
        'thumbnail_path',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentGroupUser(): HasOne
    {
        // return $this->hasOne(GroupUser::class);
        return $this->hasOne(GroupUser::class)->where('user_id', auth()->id());
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isAdminOfTheGroup(int $userId): bool
    {
        return $this->currentGroupUser->user_id === $userId;
    }
}
