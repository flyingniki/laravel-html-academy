<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'academy' => [
        'films' => [
            'url' => env('EXT_API_FILMS', 'http://academy.localhost/api/films/')
        ],
        'comments' => [
            'url' => env('EXT_API_COMMENTS', 'http://academy.localhost/api/comments/')
        ],
    ]
];
