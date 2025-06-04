<?php

namespace Ronydebnath\MCP\Types;

enum Role: string
{
    case USER = 'user';
    case ASSISTANT = 'assistant';
    case SYSTEM = 'system';
} 