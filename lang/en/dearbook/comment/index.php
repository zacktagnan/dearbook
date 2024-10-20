<?php

declare(strict_types=1);

return [
    'confirm_deletion' => [
        'question' => 'Are you sure you want to delete this comment?',
        'message' => 'Once said comment is deleted, all resources and data related to it will be permanently deleted also.',
        'button_text' => 'Delete Comment',
    ],

    'deleted_by_author' => [
        'mailing' => [
            'subject' => 'Comment deleted by author of the Post',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => 'The author of the referenced Post has deleted your comment.',
            'btn_text' => 'Access to POST',
        ],
    ],
];
