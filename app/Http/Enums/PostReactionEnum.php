<?php

namespace App\Http\Enums;

enum PostReactionEnum: string
{
    case LIKE = 'like'; // 'Me gusta' :)
        // -> Otros posibles tipos de reacción
        // ------------------------------------------
    case LOVE = 'love'; // 'Me encanta'
    // case Care = 'care'; // 'Me importa'
    // case Haha = 'haha'; // 'Me divierte'
    // case Wow = 'wow'; // 'Me asombra'
    // case Sad = 'sad'; // 'Me entristece'
    // case Angry = 'angry'; // 'Me enoja'
}
