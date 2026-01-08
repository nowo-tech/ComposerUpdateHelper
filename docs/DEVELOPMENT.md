# Development Guide

This guide covers development setup, testing, and CI/CD for Composer Update Helper.

## Development Setup

### Using Docker (Recommended)

The project includes Docker configuration for easy development:

```bash
# Start the container
make up

# Install dependencies
make install

# Run tests
make test

# Run tests with coverage
make test-coverage

# Check code style
make cs-check

# Fix code style
make cs-fix

# Run all QA checks
make qa

# Open shell in container
make shell

# Stop container
make down

# Clean build artifacts
make clean
```

### Without Docker

If you have PHP and Composer installed locally:

```bash
# Clone repository
git clone https://github.com/nowo-tech/ComposerUpdateHelper.git
cd ComposerUpdateHelper

# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Check code style
composer cs-check

# Fix code style
composer cs-fix

# Run all QA checks
composer qa
```

### Available Make Commands

| Command | Description |
|---------|-------------|
| `make up` | Start Docker container |
| `make down` | Stop Docker container |
| `make shell` | Open shell in container |
| `make install` | Install Composer dependencies |
| `make test` | Run PHPUnit tests |
| `make test-coverage` | Run tests with code coverage |
| `make cs-check` | Check code style (PSR-12) |
| `make cs-fix` | Fix code style |
| `make qa` | Run all QA checks |
| `make clean` | Remove vendor and cache |
| `make setup-hooks` | Install git pre-commit hooks |

### Pre-commit Hooks (Optional)

Install git hooks to automatically run CS-check and tests before each commit:

```bash
make setup-hooks
```

This ensures code quality checks run locally before pushing to GitHub.

## Continuous Integration

Every push to GitHub automatically triggers:

- ✅ **Tests** on PHP 7.4, 8.0, 8.1, 8.2, 8.3
- ✅ **Code Style** check (PSR-12) with automatic fixes on main/master branch
- ✅ **Code Coverage** report with **99% coverage requirement**
- ✅ **Automatic code style fixes** committed back to repository

### CI/CD Features

- **Automatic Code Style Fixes**: On push to main/master, PHP CS Fixer automatically fixes code style issues and commits them back
- **99% Code Coverage**: The CI pipeline requires 99% code coverage to pass, ensuring comprehensive test coverage (current: 99.20%)
- **Multi-PHP Testing**: Tests run on all supported PHP versions (7.4, 8.0, 8.1, 8.2, 8.3)
- **Pull Request Validation**: On pull requests, code style is checked (but not auto-fixed) to maintain code quality

See [GitHub Actions](https://github.com/nowo-tech/ComposerUpdateHelper/actions) for build status.

