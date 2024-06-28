<?php

namespace App\Traits;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Attachmentable
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    // Para eliminar, automáticamente, los Attachment al eliminar un modelo padre relacionado.
    // public static function bootAttachmentable()
    // {
    //     static::deleting(function ($model) {
    //         $model->attachments()->delete();
    //     });
    // }
}
