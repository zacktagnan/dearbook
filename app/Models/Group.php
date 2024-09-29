<?php

namespace App\Models;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Traits\CustomDateFormatting;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;
    use CustomDateFormatting;

    protected $fillable = [
        'name',
        'auto_approval',
        'type',
        'about',
        'user_id',
        'cover_path',
        'thumbnail_path',
        'deleted_by',
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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users')->where('status', GroupUserStatus::APPROVED->value);
    }

    public function requestsPending(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users')->where('status', GroupUserStatus::PENDING->value);
    }

    public function currentGroupUser(): HasOne
    {
        // return $this->hasOne(GroupUser::class);
        return $this->hasOne(GroupUser::class)->where('user_id', auth()->id());
    }

    public function allGroupUser(): HasMany
    {
        return $this->hasMany(GroupUser::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isAdminOfTheGroup(int $userId): bool
    {
        return $this->currentGroupUser->user_id === $userId;
    }

    public function adminsGroup(): BelongsToMany
    {
        // return $this->belongsToMany(User::class, 'group_users')->wherePivot('role', GroupUserRole::ADMIN->value);
        // o con un WHERE simple
        return $this->belongsToMany(User::class, 'group_users')->where('role', GroupUserRole::ADMIN->value);
    }
}
