<?php

namespace App\Models;

use App\Traits\Attachmentable;
use App\Traits\Reactionable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Attachmentable;
    use Reactionable;

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

    // public function reactions(): HasMany
    // {
    //     return $this->hasMany(PostReaction::class);
    // }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function createdAtWithFormat()
    {
        if ($this->created_at->isoFormat('Y') === date('Y')) {
            $dateWithFormat = $this->created_at->translatedFormat(config('app.format.' . app()->getLocale() . '.datetime.without_year'));
        } else {
            $dateWithFormat = $this->created_at->translatedFormat(config('app.format.' . app()->getLocale() . '.datetime.with_year'));
        }

        return $dateWithFormat;
    }

    public function createdAtWithShortFormat()
    {
        return $this->created_at->isoFormat('llll');
    }

    public function createdAtWithLargeFormat()
    {
        return $this->created_at->isoFormat('LLLL');
    }

    public function createdAtDiffForHumans()
    {
        return $this->created_at->diffForHumans();
    }

    public function updatedAtWithLargeFormat()
    {
        return $this->updated_at->isoFormat('LLLL');
    }
}
