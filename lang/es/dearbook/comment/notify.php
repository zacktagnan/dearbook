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
];
