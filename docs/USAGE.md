# Usage Guide

This guide covers all usage options and features of Composer Update Helper.

## Table of contents

- [Basic Usage](#basic-usage)
  - [Show suggested update commands](#show-suggested-update-commands)
  - [Show full release details](#show-full-release-details)
  - [Skip release information](#skip-release-information)
  - [Verbose mode](#verbose-mode)
  - [Debug mode](#debug-mode)
  - [Show help](#show-help)
  - [Execute the update commands](#execute-the-update-commands)
- [Release Information](#release-information)
  - [Release Information Options](#release-information-options)
- [Dependency Conflicts and Filtered Packages](#dependency-conflicts-and-filtered-packages)
- [Environment Variables](#environment-variables)

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
./generate-composer-require.sh --show-impact            # Show impact analysis for conflicts
./generate-composer-require.sh --save-impact            # Save impact analysis to file
./generate-composer-require.sh --verbose --release-info # Verbose with release info
./generate-composer-require.sh --show-impact --verbose  # Impact analysis with verbose output
./generate-composer-require.sh --save-impact --verbose  # Save impact to file with verbose output
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
| `--show-impact`, `--impact` | Shows impact analysis for conflicting packages (disabled by default) |
| `--save-impact` | Saves impact analysis to `composer-update-impact.txt` file (implies --show-impact, automatically added to `.gitignore`) |
| `-v, --verbose` | Shows detailed information about configuration files and packages |
| `--debug` | Shows very detailed debug information (includes verbose mode) |
| `--run` | Executes suggested commands (can be combined with other options) |
| `--help` or `-h` | Shows comprehensive usage information and examples |

## Dependency Conflicts and Filtered Packages

When dependency checking is enabled (default), the tool analyzes potential conflicts before suggesting updates. If a package update would conflict with existing dependencies, it will be filtered and shown in the output.

**Example output with filtered packages:**

```
🔧  Dependency checking analysis:
  📋 All outdated packages (before dependency check):
     - doctrine/orm:3.6.1 (prod)
     - phpdocumentor/reflection-docblock:6.0.0 (prod)
     - abandoned/package:2.0.0 (prod)

  ⚠️  Filtered by dependency conflicts:
     - doctrine/orm:3.6.1 (prod) (conflicts with 1 package: symfonycasts/reset-password-bundle requires doctrine/orm ^2.13)
     - phpdocumentor/reflection-docblock:6.0.0 (prod) (conflicts with 1 package: a2lix/auto-form-bundle requires phpdocumentor/reflection-docblock ^5.6)
     - abandoned/package:2.0.0 (prod) (conflicts with 1 package: dependency-x requires abandoned/package ^1.5) (⚠️ Package is abandoned, replaced by: new-package/name)

  💡 Alternative solutions:
     - package-a:1.9.5 (compatible with conflicting dependencies)

  💡 Suggested transitive dependency updates to resolve conflicts:
     - scheb/2fa-bundle:8.2.0 (installed: 8.1.0, required by: scheb/2fa-email:8.2.0)

  ✅ Packages that passed dependency check: (none)
```

**Understanding the output:**

1. **Conflict messages**: The format `package-a requires package-b constraint` shows which package requires which version constraint. For example: `symfonycasts/reset-password-bundle requires doctrine/orm ^2.13` means that package requires `doctrine/orm` version 2.13.x, but you're trying to update to 3.6.1.

2. **Abandoned package warnings**: When a filtered package is abandoned, a warning is shown with the replacement package (if available). Example: `(⚠️ Package is abandoned, replaced by: new-package/name)`

3. **Alternative solutions**: When fallback versions are found, they are shown as "Alternative solutions" with compatible versions that satisfy conflicting dependencies. Example: `💡 Alternative solutions: - package-a:1.9.5 (compatible with conflicting dependencies)`

4. **Transitive dependency suggestions**: When conflicts are detected, the tool may suggest updating related packages together. See the [Configuration Guide - Dependency Compatibility Checking](CONFIGURATION.md#dependency-compatibility-checking) for details.

5. **Impact analysis** (optional, use `--show-impact` flag): When enabled, shows which packages would be affected by updating conflicting packages. This includes:
   - Direct affected packages: Packages that directly depend on the conflicting package
   - Transitive affected packages: Packages that depend on directly affected packages
   - Example output:
     ```
     📊 Impact analysis: Updating package-a to 2.0 would affect:
        - dependent-package-1 (requires package-a:^1.5)
        - dependent-package-2 (requires package-a:^1.5)
        - transitive-package-1 (transitively depends on affected packages)
     ```
   - **Note**: Impact analysis is disabled by default to reduce output verbosity. Use `--show-impact` or `--impact` to enable it.
   - **Save to file**: Use `--save-impact` flag to save the impact analysis to `composer-update-impact.txt` file for later review or documentation. The file is automatically added to `.gitignore` to prevent accidental commits.

> 📖 **For a comprehensive guide to all update scenarios, conflict types, and how they are handled**, see [Update Cases and Scenarios](UPDATE_CASES.md). This document covers all 14+ supported cases, partially supported scenarios, and cases not yet implemented.

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `PHP_BIN` | Path to PHP binary | `php` |
| `COMPOSER_BIN` | Path to Composer binary | Auto-detected |

Example:

```bash
PHP_BIN=/usr/bin/php8.2 ./generate-composer-require.sh
```

