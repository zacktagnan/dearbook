<?php

declare(strict_types=1);

return [
    'section_label' => 'Grupos',

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
            'admin_text' => 'es el Administrador JEFE del grupo.',
        ],
    ],
];
