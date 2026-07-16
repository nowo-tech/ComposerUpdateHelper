# Composer Update Helper

[![CI](https://github.com/nowo-tech/ComposerUpdateHelper/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/ComposerUpdateHelper/actions/workflows/ci.yml) [![Packagist Version](https://img.shields.io/packagist/v/nowo-tech/composer-update-helper.svg?style=flat)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![Packagist Downloads](https://img.shields.io/packagist/dt/nowo-tech/composer-update-helper.svg)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE) [![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)](https://php.net) [![Symfony](https://img.shields.io/badge/Symfony-6.0%2B%20%7C%207.4%2B%20%7C%208.0%20%7C%208.1%2B-000000?logo=symfony)](https://symfony.com)

> ⭐ **Found this useful?** Install from [Packagist](https://packagist.org/packages/nowo-tech/composer-update-helper) and give the repo a [star on GitHub](https://github.com/nowo-tech/ComposerUpdateHelper) if it helps you.

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
- ✅ **Conflict Impact Analysis**: Analyzes which packages would be affected by updating conflicting packages (optional with `--show-impact` flag)
- ✅ **Save impact analysis**: Save impact analysis to file with `--save-impact` flag
- ✅ Can execute commands directly with `--run` flag
- ✅ Automatic installation via Composer plugin
- ✅ **Release information and changelogs**: Shows GitHub release links and changelog previews for outdated packages
- ✅ **Progress indicators**: Shows loading messages during long-running operations (dependency checking, fallback search, etc.)
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

**Wrapper updates:** On first install, `generate-composer-require.sh` is copied to your project root. On later `composer update` runs, the plugin **does not overwrite** a local wrapper that differs from the package copy unless you opt in:

```json
{
  "extra": {
    "composer-update-helper": {
      "auto_update_wrapper": true
    }
  }
}
```

When opt-in is disabled (default), Composer shows a comment with MD5 hashes if the wrapper differs. See [Configuration](docs/CONFIGURATION.md#composer-plugin-options).

### Architecture

The script uses a lightweight architecture for better maintainability:

- **`generate-composer-require.sh`** (in your repo): A lightweight wrapper script (about **350** lines; size may change between releases) that handles:
 - Command-line argument parsing
 - Configuration file detection
 - Executing `composer outdated`
 - Calling the PHP processor
 - Displaying formatted output from PHP
 - Extracting and executing commands for `--run` flag

- **`process-updates.php`** (in vendor): Contains the heavy processing logic (about **900** lines; size may change between releases), including:
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

# Show release information
./generate-composer-require.sh --release-info

# Show full changelogs
./generate-composer-require.sh --release-detail

# Show impact analysis for conflicting packages
./generate-composer-require.sh --show-impact

# Save impact analysis to file
./generate-composer-require.sh --save-impact

# Verbose output
./generate-composer-require.sh --verbose

# Debug mode
./generate-composer-require.sh --debug

# Show help
./generate-composer-require.sh --help
```

Example output:

```
⏭️ Ignored packages (prod):
 - doctrine/doctrine-bundle:2.13.2

🔧 Suggested commands:
 composer require --with-all-dependencies vendor/package:1.2.3 another/package:4.5.6
 composer require --dev --with-all-dependencies phpstan/phpstan:2.0.0
```

> **Note:** By default, release information is **not shown** (no API calls are made). Use `--release-info` or `--release-detail` to enable it.

**Available options:**
- `--run` - Execute suggested commands automatically
- `--release-info` - Show release information (summary with links)
- `--release-detail` - Show full release changelog for each package (implies `--release-info`)
- `--no-release-info` - Skip release information section (default behavior)
- `--show-impact, --impact` - Show impact analysis for conflicting packages (disabled by default)
- `--save-impact` - Save impact analysis to `composer-update-impact.txt` file (implies `--show-impact`)
- `-v, --verbose` - Show verbose output (configuration files, packages, etc.)
- `--debug` - Show debug information (very detailed, includes file paths, parsing, etc.)
- `-h, --help` - Show help message

For detailed usage information, see [Usage Guide](docs/USAGE.md).

## Configuration

The script searches for configuration files in the current directory (where `composer.json` is located). It supports both `.yaml` and `.yml` extensions, with `.yaml` taking priority.

Edit `generate-composer-require.yaml` to configure which packages to ignore or force include during updates, and set default values for command-line options:

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
# Supported: many locales (see docs/CONFIGURATION.md — currently 31+ languages in i18n)
# If not set, will auto-detect from system (LANG, LC_ALL, LC_MESSAGES)
# Default: en (English)
# ⚠️ WARNING: i18n feature is currently in DEVELOPMENT MODE
#language: es

# Command-line options defaults (can be overridden via command-line arguments)
# Set your preferred defaults here, then override them when needed via command-line flags
show-release-info: false     # Show release information by default
show-release-detail: false    # Show full changelog by default
show-impact-analysis: false    # Show impact analysis by default
save-impact-to-file: false    # Save impact analysis to file by default
verbose: false          # Verbose output by default
debug: false           # Debug mode by default

# List of packages to ignore during update
# Ignored packages will still be displayed in the output with their available versions,
# but won't be included in the composer require commands.
ignore:
 - doctrine/orm
 - symfony/security-bundle
 - laravel/framework
 # - package/name # You can add inline comments

# List of packages to force include during update
# Included packages will be added to the composer require commands even if they are
# in the ignore list.
# The include section has priority over the ignore section.
include:
 - some/package
 - another/package
```

> 💡 **Tip**: Command-line arguments always override YAML configuration. For example, if you set `show-release-info: true` in YAML but run `./generate-composer-require.sh --no-release-info`, the release info will be disabled for that run.

For detailed configuration options including language settings, dependency checking, and backward compatibility, see [Configuration Guide](docs/CONFIGURATION.md).

For framework support details, see [Framework Support](docs/FRAMEWORKS.md).

## Packagist Integration

Composer Update Helper fetches package information from Packagist to analyze dependencies and find compatible versions. Here's how it works:

### How It Works

The tool uses a two-tier approach for fetching package information:

1. **Primary Method**: Direct Packagist API calls (`https://packagist.org/packages/{package}.json`)
  - Fast and efficient for most use cases
  - Used for: package requirements, versions, abandoned status, maintainer info, alternative package search

2. **Fallback Method**: `composer show` command
  - Automatically used when Packagist API is unavailable or returns incomplete data
  - **Respects your project's repository configuration** in `composer.json`
  - Supports mirrors, private repositories, and custom repository setups

### Improving Packagist Access

#### Using Packagist Mirrors

If you're experiencing slow API responses or rate limiting, you can configure a Packagist mirror in your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "composer",
      "url": "https://mirror.packagist.com",
      "only": ["packagist"]
    }
  ]
}
```

The fallback method (`composer show`) will automatically use your configured mirror.

#### Using Private Repositories

For private packages or internal repositories, simply configure them in your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/your-org/private-package"
    }
  ]
}
```

When the Packagist API doesn't have information about these packages, the tool automatically falls back to `composer show`, which respects your repository configuration.

#### Performance Considerations

- **API Rate Limiting**: Packagist doesn't enforce strict rate limits, but excessive requests may be throttled. The tool includes proper user-agent headers and reasonable timeouts (5 seconds).

- **Offline Mode**: If you're working offline or behind a firewall, the tool will fall back to `composer show`, which uses Composer's local cache when available.

- **Caching**: Composer caches package metadata automatically. Running `composer update` periodically ensures your cache is fresh, improving fallback performance.

> 💡 **Tip**: If you're using a VPN or behind a corporate firewall, configuring a Packagist mirror or ensuring `composer show` works will provide the best experience.

## Requirements

- PHP `>=8.1 <8.6`
- Composer 2.x

## Version information

Supported PHP versions follow `composer.json` (`>=8.1 <8.6`). Release history is in [docs/CHANGELOG.md](docs/CHANGELOG.md).

## Documentation

- [GitHub Actions CI requirements](docs/GITHUB_CI.md)
- [Installation](docs/INSTALLATION.md)
- [Configuration](docs/CONFIGURATION.md)
- [Usage](docs/USAGE.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)
- [Changelog](docs/CHANGELOG.md)
- [Upgrading](docs/UPGRADING.md)
- [Release](docs/RELEASE.md)
- [Security](docs/SECURITY.md)
- [Engram](docs/ENGRAM.md)
- [Spec-driven development](docs/SPEC-DRIVEN-DEVELOPMENT.md)
- [GitHub Spec Kit](docs/SPEC-KIT.md)
### Additional documentation

- [Framework support](docs/FRAMEWORKS.md)
- [Update cases and scenarios](docs/UPDATE_CASES.md)
- [Testing](docs/TESTING.md)
- [Implementation roadmap](docs/IMPLEMENTATION_ROADMAP.md)
- [Development](docs/DEVELOPMENT.md)
- [Branching](docs/BRANCHING.md)
- [I18N strategy](docs/I18N_STRATEGY.md)

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

## Tests and coverage

- Tests: PHPUnit (`tests/Unit`, `tests/Integration`).
- PHP: **100%** line coverage on `src/` (refresh with `make test-coverage`).
- TS/JS: N/A
- Python: N/A

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
