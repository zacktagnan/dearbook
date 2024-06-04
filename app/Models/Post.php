<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class);
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
