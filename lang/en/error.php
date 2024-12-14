<?php

declare(strict_types=1);

return [
    '401' => [
        'title' => 'Error 401 - Unauthorized Access',
        'message' => 'Sorry. Authorization is required to access. Authentication is required.',
    ],
    '403' => [
        'title' => 'Error 403 - Forbidden Area',
        'message' => 'Sorry. You do not have permission to access this page. Contact your administrator if you think you should have permission.',
    ],
    '404' => [
        'title' => 'Error 404 - Page Not Found',
        'message' => 'Sorry. The requested page does not exist. Indicate the resource appropriately.',
    ],
    '419' => [
        'title' => 'Error 419 - Page Expired',
        'message' => 'Sorry. The authentication required for the process has expired. Please refresh your browser to proceed.',
    ],
    '500' => [
        'title' => 'Error 500 - Internal Server Fail',
        'message' => 'Whoops!! ... Something is wrong with our server. We are working on fixing it.',
    ],
    '503' => [
        'title' => 'Error 503 - Service Unavailable',
        'message' => 'Sorry. We are doing some maintenance. Please check back soon.',
    ],

    'other' => [
        'title_base' => 'Error :code - :text',
        'type' => [
            'token_not_valid' => [
                'title' => 'Link no valid',
                'message' => 'The link is not valid for the purpose for which it was intended.',
            ],
            'token_used' => [
                'title' => 'Link already used',
                'message' => 'The link has already been used at another time.',
            ],
            'token_expired' => [
                'title' => 'Link expired',
                'message' => 'The link has expired and is no longer useful.',
            ],
        ],
    ],
];
