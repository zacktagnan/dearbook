<?php

declare(strict_types=1);

return [
    'delete_option' => [
        'header' => 'Borrar grupo',
        'intro' => 'Una vez que se elimine su grupo, todos sus recursos y datos se eliminarán de forma permanente. Antes de borrar su grupo, por favor, descargue cualquier dato o información que desee conservar.',
        'modal' => [
            'question' => '¿Está seguro que quiere eliminar su grupo?',
            'text' => 'Una vez que se elimine su grupo, todos sus recursos y datos se eliminarán de forma permanente. Ingrese su contraseña para confirmar que desea eliminar su grupo de forma permanente.',
        ],
    ],

    'delete_member_option' => [
        'modal' => [
            'question' => 'Proceso de expulsión de miembro',
            'text' => 'Está a punto de expulsar al miembro ":user_name" del grupo. Confirmar para proseguir con el proceso.',
            'btn_text' => 'Expulsar miembro',
        ],
    ],

    'leave_option' => [
        'modal' => [
            'question' => 'Proceso para abandonar el grupo',
            'text' => 'Está a punto de dejar de ser miembro de este grupo. Dejará de poder gestionar toda publicación y comentario que haya efectuado dentro del grupo. Confirmar para proseguir con el proceso.',
            'btn_text' => 'Abandonar grupo',
        ],
    ],
];
