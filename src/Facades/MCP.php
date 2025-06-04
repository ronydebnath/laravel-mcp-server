<?php

namespace Ronydebnath\MCP\Facades;

use Illuminate\Support\Facades\Facade;
use Ronydebnath\MCP\Client\MCPClient;

/**
 * @method static array send(\Ronydebnath\MCP\Types\Message $message)
 * @method static array sendMultiple(array $messages)
 * 
 * @see \Ronydebnath\MCP\Client\MCPClient
 */
class MCP extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MCPClient::class;
    }
} 