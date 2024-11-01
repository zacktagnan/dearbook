<?php

declare(strict_types=1);

return [
    'deleted_by_admin' => [
        'mailing' => [
            'subject' => 'Publicación eliminada por ADMIN de grupo',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => 'Un ADMIN del grupo ":group_name" ha eliminado su publicación.',
            'btn_text' => 'Acceder al GRUPO',
        ],
    ],

    'created_on_group' => [
        'mailing' => [
            'subject' => 'Publicación creada en ":group_name"',
            'greeting' => '¿Qué tal miembro de ":group_name"?...',
            'opening_phrase' => 'En el grupo que formas parte, se ha agregado una nueva publicación.',
            'btn_text' => 'Ver la PUBLICACIÓN',
        ],
    ],

    'added_reaction' => [
        'mailing' => [
            'subject' => 'Reacción recibida por tu publicación',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => ':user_reaction_name ha reaccionado con ":type" a tu publicación.',
            'btn_text' => 'Ver la PUBLICACIÓN',
        ],
    ],

    'updated_reaction' => [
        'mailing' => [
            'subject' => 'Reacción modificada en tu publicación',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => ':user_reaction_name ha modificado a ":type" su reacción a tu publicación.',
            'btn_text' => 'Ver la PUBLICACIÓN',
        ],
    ],
];
