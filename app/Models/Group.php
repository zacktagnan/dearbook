<?php

namespace App\Models;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Traits\CustomDateFormatting;
use Illuminate\Database\Eloquent\Builder;
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
        return $this->belongsToMany(User::class, 'group_users')->where('group_users.status', GroupUserStatus::APPROVED->value);
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

    public function currentGroupUserApproved(): HasOne
    {
        return $this->hasOne(GroupUser::class)->where('user_id', auth()->id())->where('status', GroupUserStatus::APPROVED->value);
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
        return GroupUser::query()
            ->where('user_id', $userId)
            ->where('group_id', $this->id)
            ->where('role', GroupUserRole::ADMIN->value)
            ->exists();
    }

    public function isOwnerOfTheGroup(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function adminsGroup(): BelongsToMany
    {
        // return $this->belongsToMany(User::class, 'group_users')->wherePivot('role', GroupUserRole::ADMIN->value);
        // o con un WHERE simple
        return $this->belongsToMany(User::class, 'group_users')->where('role', GroupUserRole::ADMIN->value);
    }

    public function scopeFilterBySearchTerm(Builder $query, string $searchTerm): Builder
    {
        // En vez de aplicar un filtrado simple encadenado que puede producir duplicados en este caso
        //     $query->where('groups.name', 'like', '%' . $searchTerm . '%')
        //         ->orWhere('groups.about', 'like', "%$searchTerm%");
        // Aplicando un filtro WHERE agrupado sobre 'groups' para evitar posibles duplicados
        return $query->where(function ($query) use ($searchTerm) {
            $query->where('groups.name', 'like', '%' . $searchTerm . '%')
                ->orWhere('groups.about', 'like', "%$searchTerm%");
        });
    }
}
