<?php

declare(strict_types=1);

return [
    // 'section_label' => 'XXXX',

    // 'pinned_state' => [
    //     '0' => [
    //         'text' => 'Desfijada',
    //         'title' => 'Publicación desfijada',
    //     ],
    //     '1' => [
    //         'text' => 'Fijada',
    //         'title' => 'Publicación fijada',
    //     ],
    // ],
    'pinned_state' => [
        'text' => '{0} Desfijada|{1} Fijada',
        'title' => '{0} Publicación desfijada|{1} Publicación fijada',
    ],

    'options_drop_down' => [
        // 'pin' => [
        //     'action' => 'Fijar',
        //     'title' => 'Fijar publicación',
        // ],
        // 'unpin' => [
        //     'action' => 'Desfijar',
        //     'title' => 'Desfijar publicación',
        // ],
        'pin_or_unpin' => [
            'action' => '{0} Fijar|{1} Desfijar',
            'title' => '{0} Fijar publicación|{1} Desfijar publicación',
        ],
    ],
];
