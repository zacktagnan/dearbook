<?php

namespace App\Models;

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
}
