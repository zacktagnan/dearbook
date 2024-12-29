<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupAudit extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'group_id',
        'user',
        'event',
        'details',
    ];

    protected $casts = [
        'user' => 'object',
    ];
}
