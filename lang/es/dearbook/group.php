<?php

declare(strict_types=1);

return [
    'section_label' => 'Grupos',

    'form' => [
        'fields' => [
            'name' => [
                'label' => 'Nombre del grupo',
                'placeholder' => 'Nombre del grupo',
            ],
            'auto_approval' => [
                'label' => 'Aprobación automática',
            ],
            'type' => [
                'label' => [
                    'public' => 'Público',
                    'private' => 'Privado',
                ],
            ],
            'about' => [
                'label' => 'Breve descripción',
                'placeholder' => 'Breve descripción',
            ],
        ],
        'btn_init_creation' => 'Crear nuevo grupo',
        'btn_init_update' => 'Actualizar grupo',
        'btn_init_updated' => 'Actualizado.',
        'extra' => [
            'date_of_creation' => 'Día de creación:',
            'created_by' => 'Por:',
            'you' => 'tí',
        ],
    ],

    'form_invite_user' => [
        'fields' => [
            'username_or_email' => [
                'placeholder' => 'Nombre de usuario o Email',
            ],
        ],
    ],

    'search' => [
        'placeholder' => 'Teclea el grupo a buscar',
    ],

    'search_inside_profile' => [
        'placeholder' => 'Teclea el nombre de miembro a buscar',
    ],

    'list' => [
        'no_registers' => 'No estás unido a ningún grupo.',
        'no_description' => 'Descripción no disponible aún.',
        'title' => [
            'role' => [
                'admin' => 'Eres ADMIN del grupo',
            ],
            'status' => [
                'pending' => 'Solicitud pendiente de ser aprobada',
            ],
        ],
    ],

    'list_members' => [
        'no_registers' => 'No hay miembros todavía.',
    ],

    'list_requests' => [
        'no_registers' => 'No hay solicitudes pendientes.',
    ],

    'general_info' => [
        // 'x_members' => ':total miembros',
        'x_members' => '{0} :total miembros|{1} :total miembro|[2,*] :total miembros',
        'title_list_members' => '{1} Listar miembro|[2,*] Listar miembros',
        'group_by' => 'Grupo de',
        'type' => [
            'public' => 'Grupo público',
            'private' => 'Grupo privado',
        ],
        'see_more_members' => 'Ver más miembros',
    ],

    'tab_info' => [
        'info_block' => [
            'header' => 'Información sobre este grupo',
            'intro' => 'Actualice los datos de su grupo de la manera que más le convenga.',
            'no_intro' => '(descripción introductoria no disponible)',
            'features' => [
                'public' => [
                    'title' => 'Público',
                    'description' => 'Cualquiera puede ver quién es miembro del grupo y lo todo publicado.',
                ],
                'private' => [
                    'title' => 'Privado',
                    'description' => 'Solo los integrantes pueden ver quién es miembro del grupo y lo publicado.',
                ],
                'visible' => [
                    'title' => 'Visible',
                    'description' => 'Cualquiera puede encontrar el grupo.',
                ],
                'history' => [
                    'title' => 'Historial',
                    'description' => [
                        '1_3' => 'Grupo creado el :date',
                        '2_3' => 'por',
                        '3_3' => 'tí',
                    ],
                ],
            ],
        ],
        'members_block' => [
            'header' => 'Miembros',
            'admin_text' => 'es el administrador del grupo.',
        ],
    ],

    'delete_option' => [
        'header' => 'Borrar grupo',
        'intro' => 'Una vez que se elimine su grupo, todos sus recursos y datos se eliminarán de forma permanente. Antes de borrar su grupo, por favor, descargue cualquier dato o información que desee conservar.',
        'confirmation' => [
            'question' => '¿Está seguro que quiere eliminar su grupo?',
            'text' => 'Una vez que se elimine su grupo, todos sus recursos y datos se eliminarán de forma permanente. Ingrese su contraseña para confirmar que desea eliminar su grupo de forma permanente.',
        ],
    ],

    'delete_member_option' => [
        'confirmation' => [
            'question' => 'Proceso de expulsión de miembro',
            'text' => 'Está a punto de expulsar al miembro ":user_name" del grupo. Confirmar para proseguir con el proceso.',
            'btn_text' => 'Expulsar miembro',
        ],
    ],

    'process_to_join' => [
        'by_invitation' => [
            'mailing' => [
                'invitation_to_join_group' => [
                    'subject' => 'Invitación para unirse al grupo',
                    'greeting' => '¿Qué tal :user_name?...',
                    'opening_phrase' => ':admin_group te ha invitado a unirte al grupo ":group_name".',
                    'closing_phrase' => '¡¡Atención!!... El enlace expirará :num_hours horas después de recibir este mensaje.',
                    'btn_text' => 'Aceptar INVITACIÓN',
                ],
                'invitation_approved_by_user' => [
                    'subject' => 'Nuevo Miembro - Invitación aprobada por el usuario',
                    'greeting' => '¿Qué tal :admin_group?...',
                    'opening_phrase' => ':user_name ha aceptado unirse al grupo ":group_name" mediante la invitación que se le envió.',
                    'btn_text' => 'Acceder al GRUPO',
                ],
            ],
            'notification' => 'Bienvenid@ a ":group_name", acabas de unirte por invitación.',
        ],
        'by_auto_join' => [
            'notification' => 'Bienvenid@ a ":group_name" ... buena decisión la tuya.',
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
            'notification' => 'Solicitud enviada. Recibirás un email cuando sea aprobada.',
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
            'notification' => 'La solicitud de ":user_name" fue :status.',
        ],
    ],

    'member_role_change' => [
        'mailing' => [
            'subject' => 'Cambio de ROLE en el grupo',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => 'Se te asignó el ROLE de ":role" dentro del grupo ":group_name".',
            'btn_text' => 'Acceder al GRUPO',
        ],
        'notification' => 'Cambio satisfactorio de Role a ":role" para :user_name.',
    ],

    'member_removed' => [
        'mailing' => [
            'subject' => 'Expulsado del grupo',
            'greeting' => '¿Qué tal :user_name?...',
            'opening_phrase' => 'Has sido expulsado del grupo ":group_name". Lo sentimos mucho.',
            'btn_text' => 'Acceder al GRUPO',
        ],
        'notification' => 'Expulsión satisfactorio del miembro :user_name.',
    ],
];
