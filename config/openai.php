<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API Key and organization. This will be
    | used to authenticate with the OpenAI API - you can find your API key
    | and organization on your OpenAI dashboard, at https://openai.com.
    */

    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Project
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API project. This is used optionally in
    | situations where you are using a legacy user API key and need association
    | with a project. This is not required for the newer API keys.
    */
    'project' => env('OPENAI_PROJECT'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Base URL
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API base URL used to make requests. This
    | is needed if using a custom API endpoint. Defaults to: api.openai.com/v1
    */
    'base_uri' => env('OPENAI_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Default Models
    |--------------------------------------------------------------------------
    |
    | Configure the default models to use for different types of operations.
    */

    'models' => [
        'default' => env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo'),
        'advanced' => env('OPENAI_ADVANCED_MODEL', 'gpt-4-turbo-preview'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for API calls per user to prevent abuse and
    | control costs. Value is number of requests per minute.
    */

    'rate_limit_per_minute' => env('OPENAI_RATE_LIMIT_PER_MINUTE', 10),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure retry behavior for failed API calls.
    */

    'retry' => [
        'max_attempts' => env('OPENAI_MAX_RETRY_ATTEMPTS', 3),
        'delay_seconds' => env('OPENAI_RETRY_DELAY', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Generation Settings
    |--------------------------------------------------------------------------
    |
    | Configure default settings for content generation.
    */

    'generation' => [
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cost Tracking
    |--------------------------------------------------------------------------
    |
    | Enable cost tracking and set monthly limits per user tier.
    */

    'cost_tracking' => [
        'enabled' => env('OPENAI_COST_TRACKING_ENABLED', true),
        'monthly_limits' => [
            'free' => env('OPENAI_LIMIT_FREE', 5.00),        // $5 per month
            'basic' => env('OPENAI_LIMIT_BASIC', 20.00),     // $20 per month
            'pro' => env('OPENAI_LIMIT_PRO', 100.00),        // $100 per month
            'enterprise' => env('OPENAI_LIMIT_ENTERPRISE', INF), // Unlimited
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Important Chapters
    |--------------------------------------------------------------------------
    |
    | Chapters that should use the advanced model for better quality.
    */

    'important_chapters' => [
        'executive_summary',
        'market_analysis',
        'financial_plan',
        'business_model',
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Enable caching of similar prompts to reduce API calls and costs.
    */

    'cache' => [
        'enabled' => env('OPENAI_CACHE_ENABLED', true),
        'ttl' => env('OPENAI_CACHE_TTL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure logging behavior for AI operations.
    */

    'logging' => [
        'enabled' => env('OPENAI_LOGGING_ENABLED', true),
        'channel' => env('OPENAI_LOG_CHANNEL', 'stack'),
    ],
];
