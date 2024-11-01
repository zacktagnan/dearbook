<?php

declare(strict_types=1);

return [
    'deleted_by_author' => [
        'mailing' => [
            'subject' => 'Comment deleted by author of the Post',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => 'The author of the referenced Post has deleted your comment.',
            'btn_text' => 'Access to POST',
        ],
    ],

    'created_on_post' => [
        'mailing' => [
            'subject' => 'Your post has received a comment',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => ':user_comment_name has commented your post.',
            'btn_text' => 'View Post',
        ],
    ],
];
