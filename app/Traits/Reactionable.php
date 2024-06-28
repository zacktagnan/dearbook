<?php

namespace App\Traits;

use App\Models\Reaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reactionable
{
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }
}
