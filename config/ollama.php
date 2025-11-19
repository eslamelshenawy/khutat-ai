<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ollama Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for your Ollama API server. By default, Ollama runs on
    | localhost:11434. You can change this if you're running Ollama on
    | a different host or port.
    |
    */

    'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | The default model to use for general content generation.
    | Recommended: llama3.2, llama3.1, mistral, gemma2
    |
    */

    'default_model' => env('OLLAMA_DEFAULT_MODEL', 'llama3.2'),

    /*
    |--------------------------------------------------------------------------
    | Advanced Model
    |--------------------------------------------------------------------------
    |
    | A more advanced model for complex tasks like analysis and recommendations.
    | This can be the same as default_model or a larger model.
    |
    */

    'advanced_model' => env('OLLAMA_ADVANCED_MODEL', 'llama3.2'),

    /*
    |--------------------------------------------------------------------------
    | Chat Model
    |--------------------------------------------------------------------------
    |
    | Model optimized for conversational AI and chat interactions.
    |
    */

    'chat_model' => env('OLLAMA_CHAT_MODEL', 'llama3.2'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait for a response from Ollama.
    | Larger models or complex prompts may need more time.
    |
    */

    'timeout' => env('OLLAMA_TIMEOUT', 120),

    /*
    |--------------------------------------------------------------------------
    | Generation Options
    |--------------------------------------------------------------------------
    |
    | Default options for text generation.
    | - temperature: Controls randomness (0.0 = deterministic, 1.0 = creative)
    | - top_p: Nucleus sampling threshold
    | - top_k: Limits vocabulary to top K tokens
    | - num_predict: Maximum tokens to generate (-1 = unlimited)
    |
    */

    'options' => [
        'temperature' => env('OLLAMA_TEMPERATURE', 0.7),
        'top_p' => env('OLLAMA_TOP_P', 0.9),
        'top_k' => env('OLLAMA_TOP_K', 40),
        'num_predict' => env('OLLAMA_NUM_PREDICT', -1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Ollama
    |--------------------------------------------------------------------------
    |
    | Set to false to disable Ollama and prevent API calls.
    | Useful for testing or when Ollama is not available.
    |
    */

    'enabled' => env('OLLAMA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Fallback to OpenAI
    |--------------------------------------------------------------------------
    |
    | If Ollama fails or is disabled, automatically fallback to OpenAI.
    | Requires OpenAI to be configured.
    |
    */

    'fallback_to_openai' => env('OLLAMA_FALLBACK_OPENAI', false),

    /*
    |--------------------------------------------------------------------------
    | Available Models
    |--------------------------------------------------------------------------
    |
    | List of models available for selection in the UI.
    | To download a model: ollama pull model_name
    |
    */

    'available_models' => [
        'llama3.2' => 'Llama 3.2 (3B) - Fast, efficient, great for Arabic',
        'llama3.1' => 'Llama 3.1 (8B) - Balanced performance',
        'mistral' => 'Mistral 7B - Fast and capable',
        'gemma2' => 'Gemma 2 (9B) - Google\'s model',
        'qwen2.5' => 'Qwen 2.5 (7B) - Excellent for multilingual',
    ],

];
