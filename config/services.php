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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    // Add this to your existing config/services.php file
/*
|--------------------------------------------------------------------------
| Third Party Services
|--------------------------------------------------------------------------
*/

'paystack' => [
    'public_key' => env('PAYSTACK_PUBLIC_KEY'),
    'secret_key' => env('PAYSTACK_SECRET_KEY'),
    'live_mode' => env('PAYSTACK_LIVE_MODE', false),
    'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
],
// ADD BANK CONFIGURATION
'bank' => [
    'name' => env('BANK_NAME', 'First Bank of Nigeria'),
    'account_number' => env('BANK_ACCOUNT_NUMBER', '1234567890'),
    'account_name' => env('BANK_ACCOUNT_NAME', 'M-RIGHT DIGITAL SERVICES'),
    'code' => env('BANK_CODE', '011'),
    'instructions' => env('BANK_INSTRUCTIONS', 'Please use the payment reference as your transaction narration and send proof of payment to our support team.'),
],

// Add app-level configurations
'app' => [
    'receipt_generation_fee' => env('RECEIPT_GENERATION_FEE', 500),
    'resale_fee' => env('RESALE_FEE', 300),
],
// 'paystack' => [
//     'public_key' => env('PAYSTACK_PUBLIC_KEY'),
//     'secret_key' => env('PAYSTACK_SECRET_KEY'),
//     'payment_url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
//     'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
// ],
'antitheft' => [
    'api_url' => env('ANTITHEFT_API_URL', 'https://mright.com.ng/api'),
    'api_key' => env('ANTITHEFT_API_KEY'),
    'api_secret' => env('ANTITHEFT_API_SECRET'),
    'timeout' => env('ANTITHEFT_API_TIMEOUT', 30),
    'cache_duration' => env('ANTITHEFT_CACHE_DURATION', 3600), // 1 hour
    'features' => [
        'phone_checking' => env('ANTITHEFT_PHONE_CHECK', true),
        'automatic_registration' => env('ANTITHEFT_AUTO_REGISTER', true),
        'bulk_operations' => env('ANTITHEFT_BULK_OPS', true),
        'real_time_alerts' => env('ANTITHEFT_ALERTS', true),
    ],
],

'sms' => [
    'provider' => env('SMS_PROVIDER', 'termii'),
    'api_key' => env('SMS_API_KEY'),
    'sender_id' => env('SMS_SENDER_ID', 'M-RIGHT'),
    'api_url' => env('SMS_API_URL', 'https://api.ng.termii.com'),
],

/*
    |--------------------------------------------------------------------------
    | AntiTheft Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the AntiTheft external API service integration.
    | This allows your system to communicate with external antitheft systems.
    |
    */

    'antitheft' => [
        'api_url' => env('ANTITHEFT_API_URL', 'https://mright.com.ng/api'),
        'api_key' => env('ANTITHEFT_API_KEY'),
        'api_secret' => env('ANTITHEFT_API_SECRET'),
        'timeout' => env('ANTITHEFT_API_TIMEOUT', 30),
        'verify_ssl' => env('ANTITHEFT_VERIFY_SSL', true),
        
        // API Keys for incoming requests (your system's API keys)
        'api_keys' => [
            env('MRIGHT_API_KEY_1'),
            env('MRIGHT_API_KEY_2'),
            env('MRIGHT_API_KEY_3'),
        ],
        
        // Rate limiting configuration
        'rate_limits' => [
            'authenticated' => [
                'per_minute' => env('ANTITHEFT_RATE_LIMIT_MINUTE', 200),
                'per_hour' => env('ANTITHEFT_RATE_LIMIT_HOUR', 1000),
            ],
            'public' => [
                'per_minute' => env('ANTITHEFT_PUBLIC_RATE_LIMIT_MINUTE', 10),
                'per_hour' => env('ANTITHEFT_PUBLIC_RATE_LIMIT_HOUR', 100),
            ],
        ],
        
        // Monitoring and health check
        'monitoring' => [
            'enabled' => env('ANTITHEFT_MONITORING_ENABLED', true),
            'alert_email' => env('ANTITHEFT_ALERT_EMAIL', env('MAIL_FROM_ADDRESS')),
            'health_check_interval' => env('ANTITHEFT_HEALTH_CHECK_INTERVAL', 300), // 5 minutes
        ],
    ],

];
