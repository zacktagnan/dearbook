<?php

declare(strict_types=1);

return [
    'section_label' => 'Gestión de Archivo',

    'info' => 'Items in your archive are only visible to you.',

    'search' => [
        'placeholder' => 'Search activity log',
    ],

    'filters' => [
        'title' => 'Filters',
        'by' => [
            'date' => [
                'name' => 'Date',
                'option_year' => 'Year',
                'option_moth' => 'Month',
            ],
        ],
    ],

    'menu' => [
        'items' => [
            'activity_log' => 'Activity Log',
            'archive' => 'Archive',
            'trash' => 'Recycle Bin',
        ],
    ],

    'list' => [
        'no_registers' => 'Nothing to show.',
        'options' => [
            'all' => 'All',
            'archive' => [
                'short_text' => 'Archive',
                'long_text' => 'Move to archive',
            ],
            'restore' => [
                'short_text' => 'Restore',
                'long_text' => 'Restore to profile',
            ],
            'trash' => [
                'short_text' => 'To bin',
                'long_text' => 'Move to bin',
            ],
            'delete' => [
                'short_text' => 'Delete',
                'long_text' => 'Delete definitely',
            ],
        ],
    ],
];
