<?php

namespace App\Http\Enums;

enum PostReactionEnum: string
{
    case LIKE = 'like'; // 'Me gusta' :)
        // -> Otros posibles tipos de reacción
        // ------------------------------------------
    case LOVE = 'love'; // 'Me encanta'
    case CARE = 'care'; // 'Me importa'
    case HAHA = 'haha'; // 'Me divierte'
    case WOW = 'wow'; // 'Me asombra'
    case SAD = 'sad'; // 'Me entristece'
    case ANGRY = 'angry'; // 'Me enoja'
}
