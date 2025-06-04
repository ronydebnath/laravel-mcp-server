<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    |
    | This option controls the default connection that gets used while
    | interacting with MCP servers.
    |
    */
    'default' => env('MCP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | MCP Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each connection supported by the package.
    |
    */
    'connections' => [
        'default' => [
            'host' => env('MCP_HOST', 'localhost'),
            'port' => env('MCP_PORT', 8000),
            'timeout' => env('MCP_TIMEOUT', 30),
            'verify' => env('MCP_VERIFY_SSL', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the server settings for handling incoming
    | MCP requests.
    |
    */
    'server' => [
        'session_expiry' => env('MCP_SESSION_EXPIRY', 60),
        'max_connections' => env('MCP_MAX_CONNECTIONS', 100),
        'allowed_origins' => explode(',', env('MCP_ALLOWED_ORIGINS', '*')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Here you may configure the authentication settings for the MCP server.
    |
    */
    'auth' => [
        'token_expiry' => env('MCP_TOKEN_EXPIRY', 60),
        'token_length' => env('MCP_TOKEN_LENGTH', 32),
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the memory settings for storing conversation
    | history and context.
    |
    */
    'memory' => [
        'max_size' => env('MCP_MEMORY_MAX_SIZE', 100),
        'persist' => env('MCP_MEMORY_PERSIST', false),
        'storage_path' => env('MCP_MEMORY_STORAGE_PATH', storage_path('app/mcp/memory')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the message settings for the MCP server.
    |
    */
    'message' => [
        'default_type' => 'text',
        'max_length' => env('MCP_MAX_MESSAGE_LENGTH', 4096),
    ],
]; 