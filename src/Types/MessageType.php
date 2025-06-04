<?php

namespace Ronydebnath\MCP\Types;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case FILE = 'file';
} 