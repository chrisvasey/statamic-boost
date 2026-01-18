# Contributing to Statamic Boost

Thank you for considering contributing to Statamic Boost! This document outlines how to contribute effectively.

## Development Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/chrisvasey/statamic-boost.git
   cd statamic-boost
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Run tests:
   ```bash
   composer test
   ```

## Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run a specific test file
./vendor/bin/pest tests/EnvironmentDetectorTest.php
```

## Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) for code formatting:

```bash
# Check formatting
composer format

# Fix formatting issues
./vendor/bin/pint
```

## Static Analysis

We use PHPStan for static analysis:

```bash
./vendor/bin/phpstan analyse
```

## Pull Request Process

1. Fork the repository and create a feature branch from `main`
2. Write tests for any new functionality
3. Ensure all tests pass: `composer test`
4. Run code formatting: `composer format`
5. Run static analysis: `./vendor/bin/phpstan analyse`
6. Submit a pull request with a clear description of changes

## Commit Messages

- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Fix bug" not "Fixes bug")
- Keep the first line under 72 characters
- Reference issues when applicable

## Adding MCP Tools

When adding a new MCP tool:

1. Create the tool class in `src/Mcp/Tools/`
2. Extend `Laravel\Mcp\Server\Tool`
3. Add the `#[IsReadOnly]` attribute for read-only operations
4. Implement `schema()` and `handle()` methods
5. Register in `StatamicBoostServiceProvider`
6. Add corresponding tests in `tests/Feature/Tools/`

## Questions?

Feel free to open an issue or start a discussion if you have questions about contributing.
