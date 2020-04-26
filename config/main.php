<?php

use Models\User;

/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */
require_once ('preloads.php');

return [
    'app' => [
        'view' => [
            'directory' => 'views', // default views
        ]
    ],
    'user' => [
        'class' => User::class,
        'fields' => [
            'login' => 'name',
            'password' => 'password',
        ],
    ],
    'db' => require ('db.php'),
    'params' => require ('params.php'),
    'routes' => require ('routes.php'),
];
