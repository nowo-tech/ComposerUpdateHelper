# Composer Update Helper

[![CI](https://github.com/nowo-tech/composer-update-helper/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/composer-update-helper/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/nowo-tech/composer-update-helper/v)](https://packagist.org/packages/nowo-tech/composer-update-helper)
[![License](https://poser.pugx.org/nowo-tech/composer-update-helper/license)](https://packagist.org/packages/nowo-tech/composer-update-helper)
[![PHP Version Require](https://poser.pugx.org/nowo-tech/composer-update-helper/require/php)](https://packagist.org/packages/nowo-tech/composer-update-helper)

Generates `composer require` commands from outdated dependencies. Works with any PHP project: **Symfony**, **Laravel**, **Yii**, **CodeIgniter**, **Slim**, **Laminas**, etc.

## Features

- ✅ Works with any PHP project
- ✅ Separates production and development dependencies
- ✅ Shows ignored packages with available versions
- ✅ For Symfony projects: respects `extra.symfony.require` constraint
- ✅ Compares versions to avoid unnecessary updates
- ✅ Can execute commands directly with `--run` flag
- ✅ Automatic installation via Composer plugin

## Installation

```bash
composer require nowo-tech/composer-update-helper
```

After installation, two files will be copied to your project root:
- `generate-composer-require.sh` - The main script
- `generate-composer-require.ignore.txt` - Configuration file for ignored packages (only created if doesn't exist)

## Usage

### Show suggested update commands

```bash
./generate-composer-require.sh
```

Example output:

```
⏭️  Ignored packages (prod):
  - doctrine/doctrine-bundle:2.13.2

⏭️  Ignored packages (dev):
  - phpunit/phpunit:11.0.0

🔧 Suggested commands:
  composer require --with-all-dependencies vendor/package:1.2.3 another/package:4.5.6
  composer require --dev --with-all-dependencies phpstan/phpstan:2.0.0
```

### Execute the update commands

```bash
./generate-composer-require.sh --run
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

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `PHP_BIN` | Path to PHP binary | `php` |
| `COMPOSER_BIN` | Path to Composer binary | Auto-detected |

Example:

```bash
PHP_BIN=/usr/bin/php8.2 ./generate-composer-require.sh
```

## Symfony Version Constraints

For Symfony projects, the script respects the `extra.symfony.require` constraint in your `composer.json`:

```json
{
    "extra": {
        "symfony": {
            "require": "7.1.*"
        }
    }
}
```

This prevents suggesting Symfony package updates that would exceed your configured version.

## Requirements

- PHP >= 7.4
- Composer 2.x

## Development

### Setup

```bash
git clone https://github.com/nowo-tech/composer-update-helper.git
cd composer-update-helper
composer install
```

### Running Tests

```bash
# Run all tests
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

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for version history.

## Author

Created by [Héctor Franco Aceituno](https://github.com/HecFranco) at [Nowo.tech](https://nowo.tech)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
