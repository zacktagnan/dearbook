<?php

declare(strict_types=1);

return [
    'process_to_join' => [
        'by_invitation' => [
            'mailing' => [
                'to_join_group' => [
                    'subject' => 'Invitación para unirse al grupo',
                    'greeting' => '¿Qué tal :user_name?...',
                    'opening_phrase' => ':admin_group te ha invitado a unirte al grupo ":group_name".',
                    'closing_phrase' => '¡¡Atención!!... El enlace expirará :num_hours horas después de recibir este mensaje.',
                    'btn_text' => 'Aceptar INVITACIÓN',
                ],
                'approved_by_user' => [
                    'subject' => 'Nuevo Miembro - Invitación aprobada por el usuario',
                    'greeting' => '¿Qué tal :admin_group?...',
                    'opening_phrase' => ':user_name ha aceptado unirse al grupo ":group_name" mediante la invitación que se le envió.',
                    'btn_text' => 'Acceder al GRUPO',
                ],
            ],
            'web' => 'Bienvenid@ a ":group_name", acabas de unirte por invitación.',
        ],
        'by_auto_join' => [
            'web' => 'Bienvenid@ a ":group_name" ... buena decisión la tuya.',
        ],
        'by_request' => [
            'mailing' => [
                'request_to_join_group' => [
                    'subject' => 'Solicitud para unirse al grupo',
                    'greeting' => '¿Qué tal :admin_group?...',
                    'opening_phrase' => ':user_name ha solicitado unirse al grupo ":group_name".',
                    'btn_text' => 'Ir a Aprobar SOLICITUD',
                ],
            ],
            'web' => 'Solicitud enviada. Recibirás un email cuando sea aprobada.',
        ],
        'request_approved_or_not' => [
            'decision' => [
                'approved' => 'aprobada',
                'rejected' => 'rechazada',
            ],
            'mailing' => [
                'request_approved_or_not' => [
                    'subject' => 'Solicitud para unirse al grupo',
                    'greeting' => '¿Qué tal :user_name?...',
                    'opening_phrase' => 'Su solicitud para unirse al grupo ":group_name" fue :status.',
                    'closing_phrase' => [
                        'approved' => '¡Enhorabuena!',
                        'rejected' => 'Lo sentimos.',
                    ],
                    'btn_text' => 'Acceder al GRUPO',
                ],
            ],
            'web' => 'La solicitud de ":user_name" fue :status.',
        ],
    ],

    'member_role_change' => [
        'mailing' => [
            'subject' => 'Cambio de ROLE en el grupo',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => 'Se te asignó el ROLE de ":role" dentro del grupo ":group_name".',
            'btn_text' => 'Acceder al GRUPO',
        ],
        'web' => 'Cambio satisfactorio de Role a ":role" para :user_name.',
    ],

    'member_removed' => [
        'mailing' => [
            'subject' => 'Expulsado del grupo',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => 'Has sido expulsado del grupo ":group_name". Lo sentimos mucho.',
            'btn_text' => 'Acceder al GRUPO',
        ],
        'web' => 'Expulsión satisfactoria del miembro :user_name.',
    ],

    'member_leaving' => [
        'web' => 'Acaba de darse de baja del grupo :group_name.',
    ],
];
