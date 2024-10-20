<?php

declare(strict_types=1);

return [
    'confirm_deletion' => [
        'question' => 'Are you sure you want to move this post to bin?',
        'message' => 'Once in the trash, such a post will not be publicly visible. It can be archived, restored and/or deleted completely from the Archive Management section.',
        'button_text' => 'Move to Bin',
    ],
    'confirm_deletion_collection' => [
        'question' => 'Are you sure you want to move the post selected collection to bin?',
        'message' => 'Once in the trash, each of the selected posts will not be publicly visible. Each of them can be archived, restored and/or deleted completely from the Archive Management section.',
        'button_text' => 'Move to Bin Selected Posts',
    ],
    'confirm_force_deletion' => [
        'question' => 'Are you sure you want to delete your post?',
        'message' => 'Once said post is deleted, all resources and data related to it will be permanently and definitive deleted also.',
        'button_text' => 'Delete Post',
    ],
    'confirm_force_deletion_collection' => [
        'question' => 'Are you sure you want to delete the post selected collection?',
        'message' => 'Once each of the selected posts is deleted, all related resources and data for each of them will also be permanently deleted.',
        'button_text' => 'Delete Selected Posts',
    ],

    'deleted_by_admin' => [
        'mailing' => [
            'subject' => 'Post deleted by ADMIN group',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => 'An ADMIN of the ":group_name" group has deleted your post.',
            'btn_text' => 'Access to GROUP',
        ],
    ],
];
