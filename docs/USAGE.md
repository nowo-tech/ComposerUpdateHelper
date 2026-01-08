# Usage Guide

This guide covers all usage options and features of Composer Update Helper.

## Basic Usage

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

