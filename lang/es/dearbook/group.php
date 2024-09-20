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

    'general_info' => [
        'members' => 'miembros',
        'type' => [
            'public' => 'Grupo público',
            'private' => 'Grupo privado',
        ],
    ],

    'tab_info' => [
        'info_block' => [
            'header' => 'Información sobre este grupo',
            'features' => [
                'public' => [
                    'title' => 'Público',
                    'description' => 'Cualquiera puede ver quién es miembro del grupo y lo todo publicado.',
                ],
                'private' => [
                    'title' => 'Privado',
                    'description' => 'Solo los integrantes pueden ver quién es miembro del grupo y lo publicado.',
                ],
                'visible' => [
                    'title' => 'Visible',
                    'description' => 'Cualquiera puede encontrar el grupo.',
                ],
                'history' => [
                    'title' => 'Historial',
                    'description_1_2' => 'Grupo creado el :date',
                    'description_2_2' => 'por',
                ],
            ],
        ],
        'members_block' => [
            'header' => 'Miembros',
            'admin_text' => 'es el administrador del grupo.',
        ],
    ],
];
