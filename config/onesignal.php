<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OneSignal App ID
    |--------------------------------------------------------------------------
    |
    | Your OneSignal application ID (same for Android and iOS).
    |
    */

    'app_id' => env('ONESIGNAL_APP_ID', 'c7d22db0-2fd5-44b1-8447-a281faa9ec07'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal REST API Key
    |--------------------------------------------------------------------------
    |
    | Your OneSignal REST API key for server-side notifications.
    | Find it in OneSignal Dashboard > Settings > Keys & IDs.
    |
    */

    'rest_api_key' => env('ONESIGNAL_REST_API_KEY', ''),

];
