# Configuration Guide

This guide covers all configuration options for Composer Update Helper.

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

# Enable detailed dependency compatibility checking
# When enabled (true), the tool will check if proposed package versions are compatible
# with currently installed dependencies, preventing conflicts before they occur.
# When disabled (false), the tool will suggest all available updates without checking
# dependency compatibility (faster but may suggest incompatible updates).
# Default: true
check-dependencies: true

# Language for output messages
# Supported: 31 languages (en, es, pt, it, fr, de, pl, ru, ro, el, da, nl, cs, sv, no, fi, tr, zh, ja, ko, ar, hu, sk, uk, hr, bg, he, hi, vi, id, th)
# See USAGE.md for complete list with country flags
# If not set, will auto-detect from system (LANG, LC_ALL, LC_MESSAGES)
# Default: en (English)
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

If you have an old `generate-composer-require.ignore.txt` file, it will be automatically migrated to the new YAML format when you update the package. The migration works in the following scenarios:

- **YAML doesn't exist**: TXT file is migrated to YAML and then deleted
- **YAML is empty or template-only**: TXT file is migrated to YAML and then deleted
- **YAML has user-defined packages that match TXT**: TXT file is deleted (already migrated)
- **YAML has user-defined packages that differ from TXT**: YAML is preserved, TXT file remains (you can manually merge if needed)

The script also supports reading the old TXT format for backward compatibility if YAML doesn't exist.

## Language Configuration (Internationalization)

> ⚠️ **Note**: The `language` feature is currently in **development mode** and is still being reviewed and refined. While functional, translations may be incomplete and the feature should be used with caution in production environments.

The `language` option allows you to configure the output language for all messages displayed by the tool.

**Supported languages (31 total):**
- `en` - English 🇬🇧 🇺🇸 🇨🇦 🇦🇺 (default) ✅
- `es` - Spanish 🇪🇸 🇲🇽 🇦🇷 🇨🇴 ✅
- `pt` - Portuguese 🇵🇹 🇧🇷 ✅
- `it` - Italian 🇮🇹 ✅
- `fr` - French 🇫🇷 🇧🇪 🇨🇭 🇨🇦 ✅
- `de` - German 🇩🇪 🇦🇹 🇨🇭 ✅
- `pl` - Polish 🇵🇱 ✅
- `ru` - Russian 🇷🇺 ✅
- `ro` - Romanian 🇷🇴 ✅
- `el` - Greek 🇬🇷 ✅
- `da` - Danish 🇩🇰 ✅
- `nl` - Dutch 🇳🇱 🇧🇪 ✅
- `cs` - Czech 🇨🇿 ✅
- `sv` - Swedish 🇸🇪 ✅
- `no` - Norwegian 🇳🇴 ✅
- `fi` - Finnish 🇫🇮 ✅
- `tr` - Turkish 🇹🇷 ✅
- `zh` - Chinese 🇨🇳 🇹🇼 🇭🇰 ✅
- `ja` - Japanese 🇯🇵 ✅
- `ko` - Korean 🇰🇷 ✅
- `ar` - Arabic 🇸🇦 🇪🇬 🇦🇪 🇮🇶 ✅
- `hu` - Hungarian 🇭🇺 ✅
- `sk` - Slovak 🇸🇰 ✅
- `uk` - Ukrainian 🇺🇦 ✅
- `hr` - Croatian 🇭🇷 ✅
- `bg` - Bulgarian 🇧🇬 ✅
- `he` - Hebrew 🇮🇱 ✅
- `hi` - Hindi 🇮🇳 ✅
- `vi` - Vietnamese 🇻🇳 ✅
- `id` - Indonesian 🇮🇩 ✅
- `th` - Thai 🇹🇭 ✅

**Language detection priority:**
1. **YAML configuration** (highest priority): If `language: es` is set in `generate-composer-require.yaml`, that language will be used
2. **System environment variables**: If not configured, the tool detects from `LC_MESSAGES`, `LC_ALL`, or `LANG` environment variables
3. **Fallback**: If detection fails or language is not supported, defaults to English (`en`)

**To configure language:**

```yaml
# Language for output messages
# Supported: en, es, pt, it, fr, de, pl, ru, ro, el, da, nl, cs, sv, no, fi, tr, zh, ja, ko, ar, hu, sk, uk, hr, bg, he, hi, vi, id, th (31 languages)
# If not set, will auto-detect from system (LANG, LC_ALL, LC_MESSAGES)
# Default: en (English)
# ⚠️  WARNING: i18n feature is currently in DEVELOPMENT MODE
language: es
```

**Debug mode for translations:**

Use the `--debug` flag to see detailed information about language detection and translations:

```bash
./generate-composer-require.sh --debug
```

This will show:
- Detected language
- Translation function availability
- Loaded translations count
- Test translations

**Note:** Translation files are stored in the package (`vendor/nowo-tech/composer-update-helper/bin/i18n/`) and are not copied to your project root. Only `generate-composer-require.sh` is copied to the project root.

## Dependency Compatibility Checking

The `check-dependencies` option controls whether the tool performs detailed dependency compatibility checking before suggesting updates. This feature is production-ready and recommended for all users.

**Automatic features enabled when `check-dependencies: true`:**
- ✅ **Abandoned Package Detection**: Automatically detects abandoned packages via Packagist API and shows warnings with replacement suggestions
- ✅ **Fallback Version Suggestions**: Automatically searches for compatible older versions when conflicts are detected

These features require no additional configuration and work automatically. See [Usage Guide - Dependency Conflicts](USAGE.md#dependency-conflicts-and-filtered-packages) for output examples.

> 📖 **For a comprehensive guide to all update scenarios, conflict detection, and use cases**, see [Update Cases and Scenarios](UPDATE_CASES.md).

**When enabled (`check-dependencies: true`)** - Default:
- The tool analyzes `composer.lock` to identify packages that depend on the package being updated
- Verifies version constraints before suggesting updates to prevent conflicts
- If a proposed update would conflict with dependent packages, the system finds the highest compatible version
- If no compatible version exists, the update is skipped to avoid breaking dependencies
- **Automatically suggests transitive dependency updates** when conflicts are detected:
  - Detects when a package requires a newer version of a transitive dependency (e.g., `spomky-labs/otphp:^11.4` when `11.3.0` is installed)
  - Detects `self.version` constraints (e.g., `scheb/2fa-email` requiring `scheb/2fa-bundle: self.version`)
  - Generates commands that include both transitive dependencies and filtered packages together
- **Automatically detects abandoned packages** when conflicts occur (see [Usage Guide](USAGE.md#dependency-conflicts-and-filtered-packages) for output examples)
- **Automatically suggests fallback versions** when conflicts are detected (see [Usage Guide](USAGE.md#dependency-conflicts-and-filtered-packages) for output examples)
- Shows a detailed analysis section in the output with:
  - All outdated packages (before dependency check)
  - Packages filtered by dependency conflicts
  - Alternative solutions (fallback versions) when available
  - Suggested transitive dependency updates to resolve conflicts
  - Packages that passed dependency check

For detailed output examples including abandoned package warnings and fallback version suggestions, see [Usage Guide - Dependency Conflicts](USAGE.md#dependency-conflicts-and-filtered-packages).

**When disabled (`check-dependencies: false`)**:
- The tool suggests all available updates without checking dependency compatibility
- Faster execution (no dependency analysis)
- May suggest incompatible updates that could cause conflicts
- Useful when you want to see all available updates regardless of compatibility

For example output with dependency conflicts, abandoned package warnings, and fallback version suggestions, see [Usage Guide - Dependency Conflicts](USAGE.md#dependency-conflicts-and-filtered-packages).

**To disable dependency checking:**

```yaml
check-dependencies: false
```

