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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'ghn' => [
        'base_url' => env('GHN_BASE_URL', 'https://online-gateway.ghn.vn/shiip/public-api/v2'),
        'token' => env('GHN_API_TOKEN'),
        'shop_id' => env('GHN_SHOP_ID'),
        'store_district_id' => env('STORE_DISTRICT_ID'),
        'service_type_id' => (int) env('GHN_SERVICE_TYPE_ID', 2),
        'default_fee' => (int) env('GHN_DEFAULT_FEE', 30000),
        'default_item_weight' => (int) env('GHN_DEFAULT_ITEM_WEIGHT', 500),
        'quote_ttl_minutes' => (int) env('GHN_QUOTE_TTL_MINUTES', 15),
    ],

];
