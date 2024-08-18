<?php

namespace App\Libs;

class Utilities
{
    public static array $allowedMimeTypes = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'svg',
        'wav',
        'mp3',
        'mp4',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt',
        'pdf',
        'csv',
        'zip',
        'rar',
    ];

    public static int $maxTimeOnSecondsForNotificationBox = 3;

    public static string $postRootFolderBaseName = 'attachments/post-';

    public static string $commentRootFolderBaseName = 'attachments/comment-';
}
