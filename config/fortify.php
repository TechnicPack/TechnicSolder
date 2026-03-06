<?php

return [

    'guard' => 'web',

    'passwords' => 'users',

    'username' => 'email',

    'email' => 'email',

    'lowercase_usernames' => true,

    'home' => '/dashboard',

    'prefix' => '',

    'domain' => null,

    'middleware' => ['web'],

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        Laravel\Fortify\Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
            'window' => 1,
        ]),
        Laravel\Fortify\Features::resetPasswords(),
    ],

];
