<?php

namespace App\Http\Enums;

enum GroupAuditEvent: string
{
    case CREATED = 'created';
    case OWNERSHIP_TRANSFERRED = 'ownership_transferred';
}
