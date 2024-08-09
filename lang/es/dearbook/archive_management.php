<?php

declare(strict_types=1);

return [
    'section_label' => 'Gestión de Archivo',

    'info' => 'Solo tú puedes ver los elementos del archivo.',

    'search' => [
        'placeholder' => 'Buscar en registro de actividad',
    ],

    'filters' => [
        'title' => 'Filtros',
        'by' => [
            'date' => [
                'name' => 'Fecha',
                'option_year' => 'Año',
                'option_moth' => 'Mes',
            ],
        ],
    ],

    'menu' => [
        'items' => [
            'activity_log' => 'Registro de Actividad',
            'archive' => 'Archivo',
            'trash' => 'Papelera',
        ],
    ],

    'list' => [
        'no_registers' => 'No hay nada que mostrar.',
        'options' => [
            'all' => 'Todo',
            'archive' => [
                'short_text' => 'Archivar',
                'long_text' => 'Mover al archivo',
            ],
            'restore' => [
                'short_text' => 'Restaurar',
                'long_text' => 'Restaurar en el perfil',
            ],
            'trash' => [
                'short_text' => 'A papelera',
                'long_text' => 'Mover a la papelera',
            ],
            'delete' => [
                'short_text' => 'Eliminar',
                'long_text' => 'Eliminar definitivamente',
            ],
        ],
    ],
];
