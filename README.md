# Composer Update Helper

[![CI](https://github.com/nowo-tech/composer-update-helper/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/composer-update-helper/actions/workflows/ci.yml) [![Latest Stable Version](https://poser.pugx.org/nowo-tech/composer-update-helper/v)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![License](https://poser.pugx.org/nowo-tech/composer-update-helper/license)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![PHP Version Require](https://poser.pugx.org/nowo-tech/composer-update-helper/require/php)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![GitHub stars](https://img.shields.io/github/stars/nowo-tech/composer-update-helper.svg?style=social&label=Star)](https://github.com/nowo-tech/composer-update-helper)

> ⭐ **Found this project useful?** Give it a star on GitHub! It helps us maintain and improve the project.

Generates `composer require` commands from outdated dependencies. Works with any PHP project: **Symfony**, **Laravel**, **Yii**, **CodeIgniter**, **Slim**, **Laminas**, etc.

## Features

- ✅ Works with any PHP project
- ✅ Separates production and development dependencies
- ✅ Shows ignored packages with available versions
- ✅ **Multi-framework support** with version constraints:
  - **Symfony**: respects `extra.symfony.require`
  - **Laravel**: respects `laravel/framework` + `illuminate/*` versions
  - **Yii**: respects `yiisoft/yii2` version
  - **CakePHP**: respects `cakephp/cakephp` version
  - **Laminas**: respects `laminas/*` versions
  - **CodeIgniter**: respects `codeigniter4/framework` version
  - **Slim**: respects `slim/slim` version
- ✅ Compares versions to avoid unnecessary updates
- ✅ Can execute commands directly with `--run` flag
- ✅ Automatic installation via Composer plugin
- ✅ **Release information and changelogs**: Shows GitHub release links and changelog previews for outdated packages
- ✅ **Help option**: Built-in `--help` flag for comprehensive usage information
- ✅ **Performance optimized**: Emojis and common elements are optimized for better performance

## Installation

```bash
composer require --dev nowo-tech/composer-update-helper
```

After installation, two files will be copied to your project root:
- `generate-composer-require.sh` - The main script
- `generate-composer-require.ignore.txt` - Configuration file for ignored packages (only created if doesn't exist)

**Note:** These files are automatically added to your `.gitignore` during installation to prevent them from being committed to your repository.

## Usage

### Show suggested update commands

```bash
./generate-composer-require.sh
```

Example output (default mode - summary):

```
⏭️  Ignored packages (prod):
  - doctrine/doctrine-bundle:2.13.2

⏭️  Ignored packages (dev):
  - phpunit/phpunit:11.0.0

🔧 Suggested commands:
  composer require --with-all-dependencies vendor/package:1.2.3 another/package:4.5.6
  composer require --dev --with-all-dependencies phpstan/phpstan:2.0.0

📋 Release information:
  📦 vendor/package
     🔗 Release: https://github.com/vendor/package/releases/tag/v1.2.3
     📝 Changelog: https://github.com/vendor/package/releases
```

### Show full release details

```bash
./generate-composer-require.sh --release-detail
```

Example output (detailed mode):

```
📋 Release information:
  📦 vendor/package
     🔗 Release: https://github.com/vendor/package/releases/tag/v1.2.3
     📝 Changelog: https://github.com/vendor/package/releases
     📋 Release Name v1.2.3
     ──────────────────────────────────────
     What's Changed
     * Fix issue #123
     * Improve performance
     * Add new feature
     [Complete changelog...]
     ──────────────────────────────────────
```

### Skip release information

```bash
./generate-composer-require.sh --no-release-info
```

### Show help

```bash
./generate-composer-require.sh --help
# or
./generate-composer-require.sh -h
```

### Execute the update commands

```bash
./generate-composer-require.sh --run
```

You can combine options:

```bash
./generate-composer-require.sh --run --release-detail    # Execute and show full details
./generate-composer-require.sh --run --no-release-info   # Execute without release info
```

## Ignoring Packages

Edit `generate-composer-require.ignore.txt` to exclude packages from updates:

```txt
# Packages to ignore during update
# Each line is a package name (e.g.: vendor/package)

doctrine/orm
symfony/security-bundle
laravel/framework
```

Ignored packages will still be displayed in the output with their available versions, but won't be included in the `composer require` commands.

## Release Information

The script automatically fetches release information from GitHub for outdated packages:

- **Automatic detection**: Extracts GitHub repository URL from Packagist
- **Default mode**: Shows summary with release link and changelog link
- **Detailed mode** (`--release-detail`): Shows full release name and complete changelog
- **Skip option** (`--no-release-info`): Omits all release information
- **Graceful fallback**: Silently handles API failures or network issues

Release information is only fetched for packages with specific version constraints (not wildcards like `^1.0` or `~2.0`) to avoid unnecessary API calls.

### Release Information Options

| Option | Description |
|--------|-------------|
| Default (no option) | Shows summary: package name, release link, changelog link |
| `--release-detail` | Shows full release details including complete changelog |
| `--no-release-info` | Skips all release information |
| `--run` | Executes suggested commands (can be combined with other options) |
| `--help` or `-h` | Shows comprehensive usage information and examples |

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `PHP_BIN` | Path to PHP binary | `php` |
| `COMPOSER_BIN` | Path to Composer binary | Auto-detected |

Example:

```bash
PHP_BIN=/usr/bin/php8.2 ./generate-composer-require.sh
```

## Framework Version Constraints

The script automatically detects your framework and respects version constraints to prevent breaking updates.

### Symfony

Respects `extra.symfony.require` in `composer.json`:

```json
{
    "extra": {
        "symfony": {
            "require": "8.0.*"
        }
    }
}
```

### Laravel

Automatically detects `laravel/framework` version and limits all `laravel/*` and `illuminate/*` packages:

```json
{
    "require": {
        "laravel/framework": "^12.0"
    }
}
```

### Other Frameworks

| Framework | Core Package | Limited Packages |
|-----------|--------------|------------------|
| **Yii** | `yiisoft/yii2` | `yiisoft/*` |
| **CakePHP** | `cakephp/cakephp` | `cakephp/*` |
| **Laminas** | `laminas/laminas-mvc` | `laminas/*` |
| **CodeIgniter** | `codeigniter4/framework` | `codeigniter4/*` |
| **Slim** | `slim/slim` | `slim/*` |

### Example Output

```
🔧 Detected framework constraints:
  - symfony 8.0.*
  - laravel 12.0.*

⏭️  Ignored packages (prod):
  - doctrine/orm:3.0.0

🔧 Suggested commands:
  composer require --with-all-dependencies symfony/console:7.1.8
```

## Requirements

- PHP >= 7.4
- Composer 2.x

## Development

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
git clone https://github.com/nowo-tech/composer-update-helper.git
cd composer-update-helper

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
- ✅ **Code Coverage** report with **100% coverage requirement**
- ✅ **Automatic code style fixes** committed back to repository

### CI/CD Features

- **Automatic Code Style Fixes**: On push to main/master, PHP CS Fixer automatically fixes code style issues and commits them back
- **100% Code Coverage**: The CI pipeline requires 100% code coverage to pass, ensuring comprehensive test coverage
- **Multi-PHP Testing**: Tests run on all supported PHP versions (7.4, 8.0, 8.1, 8.2, 8.3)
- **Pull Request Validation**: On pull requests, code style is checked (but not auto-fixed) to maintain code quality

See [GitHub Actions](https://github.com/nowo-tech/ComposerUpdateHelper/actions) for build status.

## Contributing

Please see [docs/CONTRIBUTING.md](docs/CONTRIBUTING.md) for details.

For branching strategy, see [docs/BRANCHING.md](docs/BRANCHING.md).

## Changelog

Please see [docs/CHANGELOG.md](docs/CHANGELOG.md) for version history.

## Upgrading

Please see [docs/UPGRADING.md](docs/UPGRADING.md) for upgrade instructions and migration notes.

## Author

Created by [Héctor Franco Aceituno](https://github.com/HecFranco) at [Nowo.tech](https://nowo.tech)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
