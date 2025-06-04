# Contributing

Thank you for your interest in contributing to the MCP SDK! This document provides guidelines and instructions for contributing.

## Table of Contents

1. [Code of Conduct](#code-of-conduct)
2. [Development Setup](#development-setup)
3. [Coding Standards](#coding-standards)
4. [Testing](#testing)
5. [Documentation](#documentation)
6. [Pull Requests](#pull-requests)
7. [Release Process](#release-process)

## Code of Conduct

Please read and follow our [Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project, you agree to abide by its terms.

## Development Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- Git

### Installation

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/your-username/mcp-sdk.git
   ```
3. Install dependencies:
   ```bash
   composer install
   ```
4. Create a new branch:
   ```bash
   git checkout -b feature/your-feature
   ```

### Development Environment

1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Configure your environment variables
3. Run tests to ensure everything is working:
   ```bash
   composer test
   ```

## Coding Standards

The MCP SDK follows PSR-12 coding standards. We use PHP_CodeSniffer to enforce these standards.

### Running Code Style Checks

```bash
composer cs-check
```

### Fixing Code Style Issues

```bash
composer cs-fix
```

### IDE Configuration

#### PHPStorm

1. Install the PHP CS Fixer plugin
2. Configure the plugin to use the project's `.php-cs-fixer.php` configuration
3. Enable "On Save" formatting

#### VSCode

1. Install the PHP CS Fixer extension
2. Add the following to your settings:
   ```json
   {
       "php-cs-fixer.executablePath": "${workspaceFolder}/vendor/bin/php-cs-fixer",
       "php-cs-fixer.config": ".php-cs-fixer.php",
       "php-cs-fixer.onsave": true
   }
   ```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
composer test -- tests/Client/MCPClientTest.php

# Run tests with coverage
composer test-coverage
```

### Writing Tests

1. Follow the existing test structure
2. Use descriptive test names
3. Test both success and failure cases
4. Use data providers for multiple test cases
5. Mock external dependencies

Example:

```php
class YourTest extends TestCase
{
    public function test_something_does_something()
    {
        // Arrange
        $input = 'test';
        
        // Act
        $result = $this->subject->doSomething($input);
        
        // Assert
        $this->assertEquals('expected', $result);
    }
}
```

## Documentation

### Writing Documentation

1. Use Markdown format
2. Follow the existing documentation structure
3. Include code examples
4. Keep documentation up to date with code changes

### Building Documentation

```bash
composer docs
```

## Pull Requests

### Before Submitting

1. Ensure your code follows coding standards
2. Write or update tests
3. Update documentation
4. Run all tests
5. Check code coverage

### Pull Request Process

1. Create a new branch for your feature
2. Make your changes
3. Write tests
4. Update documentation
5. Submit a pull request
6. Wait for review

### Pull Request Template

```markdown
## Description

[Describe your changes here]

## Related Issues

[Link to related issues]

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Checklist

- [ ] My code follows the coding standards
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] I have updated the documentation
- [ ] All tests pass
- [ ] I have checked my code and corrected any misspellings
```

## Release Process

### Versioning

We follow [Semantic Versioning](https://semver.org/):

- MAJOR version for incompatible API changes
- MINOR version for backwards-compatible functionality
- PATCH version for backwards-compatible bug fixes

### Release Steps

1. Update version in `composer.json`
2. Update CHANGELOG.md
3. Create a release tag
4. Push changes to GitHub
5. Create a GitHub release

### Creating a Release

```bash
# Update version
composer version patch  # or minor, or major

# Create tag
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## Next Steps

- Read the [Getting Started](./getting-started.md) guide
- Learn about [Client Components](./client/README.md)
- Explore [Server Components](./server/README.md)
- Understand [Shared Components](./shared/README.md)
- Read about [Security](./security.md)
- Check out the [Testing](./testing.md) guide 