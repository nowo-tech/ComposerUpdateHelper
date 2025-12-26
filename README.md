# Composer Update Helper

[![CI](https://github.com/nowo-tech/composer-update-helper/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/composer-update-helper/actions/workflows/ci.yml) [![Latest Stable Version](https://poser.pugx.org/nowo-tech/composer-update-helper/v)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![License](https://poser.pugx.org/nowo-tech/composer-update-helper/license)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![PHP Version Require](https://poser.pugx.org/nowo-tech/composer-update-helper/require/php)](https://packagist.org/packages/nowo-tech/composer-update-helper) [![GitHub stars](https://img.shields.io/github/stars/nowo-tech/composer-update-helper.svg?style=social&label=Star)](https://github.com/nowo-tech/composer-update-helper)

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
- ✅ Can execute commands directly with `--run` flag
- ✅ Automatic installation via Composer plugin
- ✅ **Release information and changelogs**: Shows GitHub release links and changelog previews for outdated packages
- ✅ **Help option**: Built-in `--help` flag for comprehensive usage information
- ✅ **Verbose and Debug modes**: `-v, --verbose` and `--debug` options for troubleshooting and detailed information
- ✅ **Multiple file extensions**: Supports both `.yaml` and `.yml` extensions for configuration files
- ✅ **Performance optimized**: Emojis and common elements are optimized for better performance
- ✅ **Lightweight architecture**: Script delegates complex logic to PHP in vendor, keeping the repo script lightweight and maintainable

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

### Show suggested update commands

```bash
./generate-composer-require.sh
```

Example output (default mode - no release info):

```
⏭️  Ignored packages (prod):
  - doctrine/doctrine-bundle:2.13.2

⏭️  Ignored packages (dev):
  - phpunit/phpunit:11.0.0

🔧 Suggested commands:
  composer require --with-all-dependencies vendor/package:1.2.3 another/package:4.5.6
  composer require --dev --with-all-dependencies phpstan/phpstan:2.0.0
```

> **Note:** By default, release information is **not shown** (no API calls are made). Use `--release-info` or `--release-detail` to enable it.

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

### Verbose mode

Show detailed information about configuration files and packages:

```bash
./generate-composer-require.sh --verbose
# or
./generate-composer-require.sh -v
```

Example output:

```
📋 Found configuration file: generate-composer-require.yaml
📋 Ignored packages: doctrine/orm, symfony/security-bundle
📋 Included packages: monolog/monolog
```

### Debug mode

Show very detailed debug information (includes verbose mode):

```bash
./generate-composer-require.sh --debug
```

Example output:

```
🔍 DEBUG: Current directory: /path/to/project
🔍 DEBUG: Searching for configuration files:
   - generate-composer-require.yaml
   - generate-composer-require.yml
   - generate-composer-require.ignore.txt
📋 Found configuration file: generate-composer-require.yaml
🔍 DEBUG: Processing YAML file: generate-composer-require.yaml
🔍 DEBUG: File exists: yes
🔍 DEBUG: File size: 512 bytes
🔍 DEBUG: Ignored packages from YAML: doctrine/orm|symfony/security-bundle
🔍 DEBUG: Ignored packages list:
   - doctrine/orm
   - symfony/security-bundle
...
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
./generate-composer-require.sh --run                    # Execute (no release info by default)
./generate-composer-require.sh --run --release-info     # Execute with release info
./generate-composer-require.sh --run --release-detail   # Execute with full changelog
./generate-composer-require.sh --verbose --release-info # Verbose with release info
./generate-composer-require.sh --debug                  # Debug mode (very detailed)
```

## Package Configuration

The script searches for configuration files in the current directory (where `composer.json` is located). It supports both `.yaml` and `.yml` extensions, with `.yaml` taking priority.

**Supported configuration files (in order of priority):**
1. `generate-composer-require.yaml` (preferred)
2. `generate-composer-require.yml` (alternative)
3. `generate-composer-require.ignore.txt` (backward compatibility)

Edit `generate-composer-require.yaml` (or `.yml`) to configure which packages to ignore or force include during updates:

```yaml
# Composer Update Helper Configuration
# Configuration file for ignored and included packages during composer update suggestions

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

### Ignoring Packages

Packages listed in the `ignore` section will:
- Still be displayed in the output with their available versions
- **Not** be included in the `composer require` commands
- Appear in the "Ignored" section of the output

**Important:** Only uncommented packages are read. Lines starting with `#` are ignored (they are comments). To ignore a package, it must be listed without the `#` prefix:

```yaml
ignore:
  - doctrine/orm                    # ✅ This package will be ignored
  # - symfony/security-bundle      # ❌ This is a comment, not read
```

### Forcing Package Inclusion

Packages listed in the `include` section will:
- **Always** be included in the `composer require` commands
- Override the `ignore` list (if a package is in both, it will be included)
- Be processed even if they are also listed in the `ignore` section

**Important:** Only uncommented packages are read. Lines starting with `#` are ignored (they are comments). To force include a package, it must be listed without the `#` prefix:

```yaml
include:
  - monolog/monolog                 # ✅ This package will be force included
  # - another/package               # ❌ This is a comment, not read
```

**Example use case**: You might want to ignore most Symfony packages but force include a specific one:

```yaml
ignore:
  - symfony/*  # Ignore all Symfony packages

include:
  - symfony/security-bundle  # But force include this one
```

### Backward Compatibility

If you have an old `generate-composer-require.ignore.txt` file, it will be automatically migrated to the new YAML format when you update the package. The migration works even if a YAML file already exists (as long as it's empty or contains only the template). The script also supports reading the old TXT format for backward compatibility if YAML doesn't exist.

## Release Information

The script automatically fetches release information from GitHub for outdated packages:

- **Automatic detection**: Extracts GitHub repository URL from Packagist
- **Default mode** (disabled by default): No release information is shown (no API calls are made)
- **Summary mode** (`--release-info`): Shows summary with release link and changelog link
- **Detailed mode** (`--release-detail`): Shows full release name and complete changelog
- **Skip option** (`--no-release-info`): Explicitly omits all release information (default behavior)
- **Graceful fallback**: Silently handles API failures or network issues

**Note:** Release information is only fetched for packages with specific version constraints (not wildcards like `^1.0` or `~2.0`) to avoid unnecessary API calls. By default, no API calls are made, improving performance. Use `--release-info` or `--release-detail` to enable release information.

### Release Information Options

| Option | Description |
|--------|-------------|
| Default (no option) | No release information shown (no API calls, better performance) |
| `--release-info` | Shows summary: package name, release link, changelog link |
| `--release-detail` | Shows full release details including complete changelog |
| `--no-release-info` | Explicitly skips all release information (default behavior) |
| `-v, --verbose` | Shows detailed information about configuration files and packages |
| `--debug` | Shows very detailed debug information (includes verbose mode) |
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
