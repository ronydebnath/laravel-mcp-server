# MCP SDK Examples

This directory contains example code demonstrating how to use the MCP SDK in various scenarios.

## Directory Structure

- `clients/` - Examples of using the MCP client components
- `servers/` - Examples of implementing MCP servers
- `shared/` - Examples of using shared components

## Getting Started

1. Make sure you have the MCP SDK installed:
   ```bash
   composer require ronydebnath/mcp-sdk
   ```

2. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

3. Configure your environment variables in `.env`

4. Run the examples:
   ```bash
   php examples/clients/basic-client.php
   ```

## Examples

### Client Examples

- [Basic Client](clients/basic-client.php) - Simple client usage
- [Streaming Client](clients/streaming-client.php) - Streaming client usage
- [Custom Client](clients/custom-client.php) - Custom client implementation

### Server Examples

- [Basic Server](servers/basic-server.php) - Simple server implementation
- [Custom Server](servers/custom-server.php) - Custom server implementation
- [Auth Server](servers/auth-server.php) - Server with authentication

### Shared Examples

- [Memory Usage](shared/memory-usage.php) - Memory management examples
- [Progress Tracking](shared/progress-tracking.php) - Progress tracking examples
- [Context Management](shared/context-management.php) - Context management examples

## Contributing

Feel free to submit new examples or improve existing ones. Please follow the [contributing guidelines](../docs/contributing.md). 