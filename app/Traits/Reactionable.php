<?php

namespace App\Traits;

use App\Models\Reaction;

trait Reactionable
{
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }
}
