<?php

return [
    (object)[
        'name' => 'Dashboard',
        'icon' => 'bx bx-home-circle',
        'url' => 'dashboard-analytics',
        "permissions" => "dashboard",
        'slug' => 'dashboard',
    ],

    (object)[
        'name' => 'admins_Management',
        'icon' => 'bx bx-group',
        'url' => 'users.index',
        "permissions" => "admins_read",
        'slug' => 'users',
        'submenu' => [
            (object)[
                'name' => 'Admins',
                'icon' => 'bx bx-shield-quarter',
                'url' => 'admins.index',
                "permissions" => "admins_read",
                'slug' => 'admins',
            ],
            (object)[
                'name' => 'Create_admin',
                'url' => 'admins.create',
                "permissions" => "admins_create",
                'slug' => 'admins.create',
            ]
        ]
    ],

    (object)[
        'name' => 'users_management',
        'icon' => 'bx bx-user',
        'url' => 'users.index',
        "permissions" => "user_read",
        'slug' => 'user',
        'submenu' => [
            (object)[
                'name' => 'Users',
                'icon' => 'bx bx-user',
                'url' => 'users.index',
                "permissions" => "user_read",
                'slug' => 'users',
            ],

        ]
    ],
    (object)[
        'name' => 'role',
        'icon' => 'bx bx-user',
        'url' => 'Role.index',
        "permissions" => "role_read",
        'slug' => 'role',
        'submenu' => [
            (object)[
                'name' => 'role',
                'url' => 'Role.index',
                "permissions" => "role_read",
                'slug' => 'Role',
            ],
        ]
    ],
];
