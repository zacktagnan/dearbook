<?php

declare(strict_types=1);

return [
    'section_label' => 'Grupos',

    'create' => [
        'fields' => [
            'name' => [
                'placeholder' => 'Nombre del grupo',
            ],
            'auto_approval' => [
                'label' => 'Aprobación automática',
            ],
            'about' => [
                'placeholder' => 'Breve descripción',
            ],
        ],
        'btn_init_creation' => 'Crear nuevo grupo',
    ],

    'search' => [
        'placeholder' => 'Teclea el grupo a buscar',
    ],

    'list' => [
        'no_registers' => 'No estás unido a ningún grupo.',
        'no_description' => 'Descripción no disponible aún.',
        'title' => [
            'role' => [
                'admin' => 'Eres ADMIN del grupo',
            ],
            'status' => [
                'pending' => 'Solicitud pendiente de ser aprobada',
            ],
        ],
    ],
];
