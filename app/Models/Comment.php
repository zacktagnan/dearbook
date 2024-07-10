<?php

namespace App\Models;

use App\Traits\Attachmentable;
use App\Traits\CustomDateFormatting;
use App\Traits\Reactionable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;
    use Attachmentable;
    use Reactionable;
    use CustomDateFormatting;

    protected $fillable = [
        'post_id',
        'comment',
        'user_id',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
