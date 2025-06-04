<?php

namespace Ronydebnath\MCP\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    public function __construct(string $message = 'Authentication failed', int $code = 401)
    {
        parent::__construct($message, $code);
    }
} 