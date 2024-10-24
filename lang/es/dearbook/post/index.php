<?php

declare(strict_types=1);

return [
    'confirm_deletion' => [
        'question' => '¿Está seguro que desea mandar esta publicación a la papelera?',
        'message' => 'Una vez en la papelera, dicha publicación no será visible públicamente. Podrá ser archivada, restaurada y/o eliminada por completo desde la sección de Gestión de Archivo.',
        'button_text' => 'Mandar a la Papelera',
    ],
    'confirm_deletion_collection' => [
        'question' => '¿Está seguro que desea mandar a la papelera el conjunto de publicaciones seleccionado?',
        'message' => 'Una vez en la papelera, cada una de las publicaciones elegidas no será visible públicamente. Cada una de ellas podrá ser archivada, restaurada y/o eliminada por completo desde la sección de Gestión de Archivo.',
        'button_text' => 'Mandar a la Papelera Publicaciones Seleccionadas',
    ],
    'confirm_force_deletion' => [
        'question' => '¿Está seguro que desea eliminar esta publicación?',
        'message' => 'Una vez que se elimine dicha publicación, igualmente, todos los recursos y datos relacionados con ella serán eliminados de forma permanente y definitiva.',
        'button_text' => 'Eliminar Publicación',
    ],
    'confirm_force_deletion_collection' => [
        'question' => '¿Está seguro que desea eliminar el conjunto de publicaciones seleccionado?',
        'message' => 'Una vez que se elimine cada una de las publicaciones seleccionadas, igualmente, todos los recursos y datos relacionados de cada una de ellas serán eliminados de forma permanente y definitiva.',
        'button_text' => 'Eliminar Publicaciones Seleccionadas',
    ],

    'deleted_by_admin' => [
        'mailing' => [
            'subject' => 'Publicación eliminada por ADMIN de grupo',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => 'Un ADMIN del grupo ":group_name" ha eliminado su publicación.',
            'btn_text' => 'Acceder al GRUPO',
        ],
    ],
];
