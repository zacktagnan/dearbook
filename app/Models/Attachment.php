<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'path',
        'mime',
        'size',
        'created_by',
    ];

    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function (self $model) {
            if (Storage::disk('public')->exists($model->path)) {
                Storage::disk('public')->delete($model->path);
            }
        });
    }

    public function scopeForUserOrGroup($query, $userId = null, $groupId = null): Builder
    {
        return $query->where('mime', 'like', 'image/%')
            ->when($userId, function ($query) use ($userId) {
                $query->where('created_by', $userId);
            })
            ->when($groupId, function ($query) use ($groupId) {
                $query->whereHas('attachmentable', function ($q) use ($groupId) {
                    $q->where('group_id', $groupId);
                });
            })
            ->with('attachmentable')
            ->latest();
    }
}
