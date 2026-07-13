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

    'stripe' => [
        'secret' => env('STRIPE_SECRET', 'mock_secret_key'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'bkash' => [
        'app_key' => env('BKASH_APP_KEY', 'mock_app_key'),
        'app_secret' => env('BKASH_APP_SECRET', 'mock_app_secret'),
        'username' => env('BKASH_USERNAME', 'mock_username'),
        'password' => env('BKASH_PASSWORD', 'mock_password'),
        'base_url' => env('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'),
    ],

];
