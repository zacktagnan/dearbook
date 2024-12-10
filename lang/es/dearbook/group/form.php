<?php

declare(strict_types=1);

return [
    'create' => [
        'fields' => [
            'name' => [
                'label' => 'Nombre del grupo',
                'placeholder' => 'Nombre del grupo',
            ],
            'auto_approval' => [
                'label' => 'Aprobación automática',
            ],
            'type' => [
                'label' => [
                    'public' => 'Público',
                    'private' => 'Privado',
                ],
            ],
            'about' => [
                'label' => 'Breve descripción',
                'placeholder' => 'Breve descripción',
            ],
        ],
        'btn_reset' => 'Resetear',
        'btn_init_creation' => 'Crear nuevo grupo',
        'btn_init_update' => 'Actualizar grupo',
        'btn_init_updated' => 'Actualizado.',
        'extra' => [
            'date_of_creation' => 'Día de creación:',
            'created_by' => 'Por:',
            'you' => 'tí',
        ],
    ],

    'invite_user' => [
        'fields' => [
            'username_or_email' => [
                'placeholder' => 'Nombre de usuario o Email',
            ],
        ],
    ],
];
