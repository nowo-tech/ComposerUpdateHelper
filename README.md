# Composer Update Helper

![CI](https://github.com/nowo-tech/ComposerUpdateHelper/actions/workflows/ci.yml/badge.svg) ![Latest Stable Version](https://poser.pugx.org/nowo-tech/composer-update-helper/v) ![License](https://poser.pugx.org/nowo-tech/composer-update-helper/license) ![PHP Version Require](https://poser.pugx.org/nowo-tech/composer-update-helper/require/php) ![GitHub stars](https://img.shields.io/github/stars/nowo-tech/ComposerUpdateHelper.svg?style=social&label=Star)

> ⭐ **Found this project useful?** Give it a star on GitHub! It helps us maintain and improve the project.

Generates `composer require` commands from outdated dependencies. Works with any PHP project: **Symfony**, **Laravel**, **Yii**, **CodeIgniter**, **Slim**, **Laminas**, etc.

## Features

- ✅ Works with any PHP project
- ✅ Separates production and development dependencies
- ✅ Shows ignored packages with available versions
- ✅ **Force include packages**: Override ignore list to force specific packages to be included
- ✅ **Multi-framework support** with version constraints:
  - **Symfony**: respects `extra.symfony.require`
  - **Laravel**: respects `laravel/framework` + `illuminate/*` versions
  - **Yii**: respects `yiisoft/yii2` version
  - **CakePHP**: respects `cakephp/cakephp` version
  - **Laminas**: respects `laminas/*` versions
  - **CodeIgniter**: respects `codeigniter4/framework` version
  - **Slim**: respects `slim/slim` version
- ✅ Compares versions to avoid unnecessary updates
- ✅ **Dependency compatibility checking**: Automatically detects and prevents dependency conflicts before suggesting updates
- ✅ **Transitive dependency suggestions**: When conflicts are detected, automatically suggests updating required transitive dependencies with ready-to-use commands
- ✅ Can execute commands directly with `--run` flag
- ✅ Automatic installation via Composer plugin
- ✅ **Release information and changelogs**: Shows GitHub release links and changelog previews for outdated packages
- ✅ **Progress indicators**: Animated spinner shows activity during long-running operations
- ✅ **Help option**: Built-in `--help` flag for comprehensive usage information
- ✅ **Verbose and Debug modes**: `-v, --verbose` and `--debug` options for troubleshooting and detailed information
- ✅ **Multiple file extensions**: Supports both `.yaml` and `.yml` extensions for configuration files
- ✅ **Performance optimized**: Emojis and common elements are optimized for better performance
- ✅ **Lightweight architecture**: Script delegates complex logic to PHP in vendor, keeping the repo script lightweight and maintainable
- ⚠️ **Internationalization (i18n)** (DEVELOPMENT MODE): Multi-language support for output messages with automatic language detection

## Installation

```bash
composer require --dev nowo-tech/composer-update-helper
```

> 💡 **Tip**: We also recommend installing [Code Review Guardian](https://github.com/nowo-tech/CodeReviewGuardian) for a complete code quality workflow. See [Related Packages](#related-packages) section below.

After installation, two files will be copied to your project root:
- `generate-composer-require.sh` - The lightweight wrapper script (delegates complex logic to PHP in vendor)
- `generate-composer-require.yaml` - Configuration file for ignored and included packages (only created if doesn't exist)

**Note:** These files should be committed to your repository so they're available to all team members. The plugin will remove any old `.ignore.txt` entries from `.gitignore` if they exist.

**Auto-update:** The `generate-composer-require.sh` script is automatically updated when you run `composer update` if the content differs from the version in vendor. This ensures you always have the latest version of the script.

### Architecture

The script uses a lightweight architecture for better maintainability:

- **`generate-composer-require.sh`** (in your repo): A lightweight wrapper script (~283 lines) that handles:
  - Command-line argument parsing
  - Configuration file detection
  - Executing `composer outdated`
  - Calling the PHP processor
  - Displaying formatted output from PHP
  - Extracting and executing commands for `--run` flag

- **`process-updates.php`** (in vendor): Contains all the complex logic (~710 lines) including:
  - Package processing and filtering
  - Framework detection and version constraints
  - Release information fetching
  - Command generation
  - **Output formatting** (emojis, sections, formatting, etc.)

The script automatically detects `process-updates.php` in `vendor/nowo-tech/composer-update-helper/bin/` and uses it. This architecture ensures:
- ✅ **Lightweight script in your repo**: Easy to read and understand
- ✅ **Complex logic in vendor**: Automatically updated with `composer update`
- ✅ **Better maintainability**: Clear separation of concerns
- ✅ **Automatic detection**: No configuration needed

## Usage

### Basic Usage

```bash
# Show suggested update commands
./generate-composer-require.sh

# Execute commands directly
./generate-composer-require.sh --run

# Show help
./generate-composer-require.sh --help
```

Example output:

```
⏭️  Ignored packages (prod):
  - doctrine/doctrine-bundle:2.13.2

🔧 Suggested commands:
  composer require --with-all-dependencies vendor/package:1.2.3 another/package:4.5.6
  composer require --dev --with-all-dependencies phpstan/phpstan:2.0.0
```

> **Note:** By default, release information is **not shown** (no API calls are made). Use `--release-info` or `--release-detail` to enable it.

**Available options:**
- `--run` - Execute suggested commands
- `--release-info` - Show release summary
- `--release-detail` - Show full changelog
- `-v, --verbose` - Show detailed information
- `--debug` - Show debug information
- `-h, --help` - Show help

For detailed usage information, see [Usage Guide](docs/USAGE.md).

## Configuration

The script searches for configuration files in the current directory (where `composer.json` is located). It supports both `.yaml` and `.yml` extensions, with `.yaml` taking priority.

Edit `generate-composer-require.yaml` to configure which packages to ignore or force include during updates:

```yaml
# Composer Update Helper Configuration
# Configuration file for ignored and included packages during composer update suggestions

# Enable detailed dependency compatibility checking
# When enabled (true), the tool will check if proposed package versions are compatible
# with currently installed dependencies, preventing conflicts before they occur.
# When disabled (false), the tool will suggest all available updates without checking
# dependency compatibility (faster but may suggest incompatible updates).
# Default: true
check-dependencies: true

# Language for output messages
# Supported: en (English), es (Spanish), pt (Portuguese), it (Italian), fr (French), de (German), pl (Polish), ru (Russian), ro (Romanian), el (Greek), da (Danish)
# If not set, will auto-detect from system (LANG, LC_ALL, LC_MESSAGES)
# Default: en (English)
# ⚠️  WARNING: i18n feature is currently in DEVELOPMENT MODE
#language: es

# List of packages to ignore during update
# Ignored packages will still be displayed in the output with their available versions,
# but won't be included in the composer require commands.
ignore:
  - doctrine/orm
  - symfony/security-bundle
  - laravel/framework
  # - package/name  # You can add inline comments

# List of packages to force include during update
# Included packages will be added to the composer require commands even if they are
# in the ignore list.
# The include section has priority over the ignore section.
include:
  - some/package
  - another/package
```

For detailed configuration options including language settings, dependency checking, and backward compatibility, see [Configuration Guide](docs/CONFIGURATION.md).

For framework support details, see [Framework Support](docs/FRAMEWORKS.md).

## Requirements

- PHP >= 7.4
- Composer 2.x

## Documentation

All documentation is available in the [`docs/`](docs/) directory:

### User Guides
- **[Usage Guide](docs/USAGE.md)** - Complete usage instructions and options
- **[Configuration Guide](docs/CONFIGURATION.md)** - Configuration options and settings
- **[Framework Support](docs/FRAMEWORKS.md)** - Framework version constraints and support
- **[Update Cases and Scenarios](docs/UPDATE_CASES.md)** - Comprehensive guide to all supported update scenarios and use cases

### Project Documentation
- **[CHANGELOG.md](docs/CHANGELOG.md)** - Version history and all notable changes
- **[UPGRADING.md](docs/UPGRADING.md)** - Upgrade instructions and migration notes
- **[Development Guide](docs/DEVELOPMENT.md)** - Development setup, testing, and CI/CD
- **[CONTRIBUTING.md](docs/CONTRIBUTING.md)** - Guidelines for contributing to the project
- **[BRANCHING.md](docs/BRANCHING.md)** - Branching strategy and workflow
- **[I18N_STRATEGY.md](docs/I18N_STRATEGY.md)** - Internationalization (i18n) implementation strategy
- **[LANGUAGES_PROPOSAL.md](docs/LANGUAGES_PROPOSAL.md)** - Additional languages proposal for future implementation
- **[VERIFICATION.md](docs/VERIFICATION.md)** - Verification and testing documentation

## Contributing

Please see [docs/CONTRIBUTING.md](docs/CONTRIBUTING.md) for details.

For branching strategy, see [docs/BRANCHING.md](docs/BRANCHING.md).

## Changelog

Please see [docs/CHANGELOG.md](docs/CHANGELOG.md) for version history.

## Upgrading

Please see [docs/UPGRADING.md](docs/UPGRADING.md) for upgrade instructions and migration notes.

## Related Packages

### Code Review Guardian

Looking for a complete code review solution? We highly recommend **[Code Review Guardian](https://github.com/nowo-tech/CodeReviewGuardian)** - a provider-agnostic code review guardian that works perfectly with Composer Update Helper:

- ✅ **Provider-agnostic**: Works with GitHub, GitLab, Bitbucket, and any Git provider
- ✅ **Multi-framework support**: Automatic framework detection (Symfony, Laravel, etc.)
- ✅ **Code quality checks**: PHP-CS-Fixer, PHPStan, PHPUnit, Security checks
- ✅ **Easy integration**: Simple YAML configuration
- ✅ **Framework-specific configs**: Optimized configurations for each framework

**Installation:**
```bash
composer require --dev nowo-tech/code-review-guardian
```

**Why use both together?**

Together with Composer Update Helper, you get a complete development workflow:

1. **Composer Update Helper** → Keeps your dependencies up to date
   - Automatically detects outdated packages
   - Generates update commands
   - Respects framework version constraints

2. **Code Review Guardian** → Ensures code quality in your pull requests
   - Runs code quality checks automatically
   - Validates code style and standards
   - Prevents merging low-quality code

**Perfect combination for maintaining high-quality PHP projects!** 🚀

## Author

Created by [Héctor Franco Aceituno](https://github.com/HecFranco) at [Nowo.tech](https://nowo.tech)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
