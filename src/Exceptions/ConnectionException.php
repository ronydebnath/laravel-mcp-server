<?php

namespace Ronydebnath\MCP\Exceptions;

use Exception;

class ConnectionException extends Exception
{
    public function __construct(string $message = "Failed to connect to MCP server", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 