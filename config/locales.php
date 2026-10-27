<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | First visit (no session preference) uses this language.
    | Users can switch via the header language switcher (/locale/{code}).
    |
    */

    // First visit always English; user choice is stored in session via /locale/{code}.
    'default' => 'en',

    'fallback' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Keys are BCP 47 language codes used in lang/{code}.json files.
    |
    */

    'supported' => [
        'en' => [
            'code' => 'EN',
            'label' => 'English',
            'native' => 'English',
        ],
        'tr' => [
            'code' => 'TR',
            'label' => 'Turkish',
            'native' => 'Türkçe',
        ],
    ],

];
