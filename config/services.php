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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'trendyol' => [
        'seller_id'  => env('TRENDYOL_SELLER_ID'),
        'api_key'    => env('TRENDYOL_API_KEY'),
        'api_secret' => env('TRENDYOL_API_SECRET'),
        'base_url'   => env('TRENDYOL_BASE_URL', 'https://apigw.trendyol.com'),
        'user_agent' => env('TRENDYOL_USER_AGENT', 'Discody Integration'),
    ],

    'apisports' => [
        'key' => env('APISPORTS_KEY', '9114722b0aeaffd0b4967cc5054fc123'),
    ],

];
