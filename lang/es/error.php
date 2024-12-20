<?php

declare(strict_types=1);

return [
    '401' => [
        'title' => 'Error 401 - Acceso No Autorizado',
        'message' => 'Lo sentimos. Se requiere una autorización para acceder. Es necesario autenticarse.',
    ],
    '403' => [
        'title' => 'Error 403 - Área No Permitida',
        'message' => 'Lo sentimos. Sin permiso para acceder a esta página. Contacte con su administrador si piensa que tuviera que tener permiso.',
    ],
    '404' => [
        'title' => 'Error 404 - Página No Encontrada',
        'message' => 'Lo sentimos. La página solicitada no existe. Indique el recurso de forma adecuada.',
    ],
    '419' => [
        'title' => 'Error 419 - Página Expirada',
        'message' => 'Lo sentimos. La autenticación necesaria para el proceso ha expirado. Actualizar el navegador para seguir adelante.',
    ],
    '500' => [
        'title' => 'Error 500 - Fallo Interno de Servidor',
        'message' => '¡¡Vaya!! ... Algo va mal en nuestro servidor. Estamos trabajando para solucionarlo.',
    ],
    '503' => [
        'title' => 'Error 503 - Servicio No Disponible',
        'message' => 'Lo sentimos. Estamos realizando tareas de mantenimiento. Vuelva más tarde.',
    ],

    'other' => [
        'title_base' => 'Error :code - :text',
        'type' => [
            'token_not_valid' => [
                'title' => 'Enlace no válido',
                'message' => 'El enlace no es válido para el propósito al que estaba destinado.',
            ],
            'token_used' => [
                'title' => 'Enlace ya empleado',
                'message' => 'El enlace ya ha sido empleado en otro momento.',
            ],
            'token_expired' => [
                'title' => 'Enlace expirado',
                'message' => 'El enlace ha expirado y no sirve.',
            ],
        ],
    ],
];
