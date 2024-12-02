<?php

declare(strict_types=1);

return [
    // 'section_label' => 'XXXX',

    // 'pinned_state' => [
    //     '0' => [
    //         'text' => 'Unpinned',
    //         'title' => 'Post unpinned',
    //     ],
    //     '1' => [
    //         'text' => 'Pinned',
    //         'title' => 'Post pinned',
    //     ],
    // ],
    'pinned_state' => [
        'text' => '{0} Unpinned|{1} Pinned',
        'title' => '{0} Post unpinned|{1} Post pinned',
    ],

    'options_drop_down' => [
        // 'pin' => [
        //     'action' => 'Pin',
        //     'title' => 'Pin post',
        // ],
        // 'unpin' => [
        //     'action' => 'Unpin',
        //     'title' => 'Unpin post',
        // ],
        'pin_or_unpin' => [
            'action' => '{0} Pin|{1} Unpin',
            'title' => '{0} Pin post|{1} Unpin post',
        ],
    ],
];
