<?php

declare(strict_types=1);

return [
    'deleted_by_author' => [
        'mailing' => [
            'subject' => 'Comentario eliminado por autor del Post',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => 'El autor del Post referenciado ha eliminado su comentario.',
            'btn_text' => 'Acceder a la PUBLICACIÓN',
        ],
    ],

    'created_on_post' => [
        'mailing' => [
            'subject' => 'Tu publicación recibió un comentario',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => ':user_comment_name ha comentado tu publicación.',
            'btn_text' => 'Ver la PUBLICACIÓN',
        ],
    ],

    'added_reaction' => [
        'mailing' => [
            'subject' => 'Reacción recibida por tu comentario',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => ':user_reaction_name ha reaccionado con ":type" a tu comentario.',
            'btn_text' => 'Ver la PUBLICACIÓN',
        ],
    ],

    'updated_reaction' => [
        'mailing' => [
            'subject' => 'Reacción modificada en tu comentario',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => ':user_reaction_name ha modificado a ":type" su reacción a tu comentario.',
            'btn_text' => 'Ver la PUBLICACIÓN',
        ],
    ],
];
