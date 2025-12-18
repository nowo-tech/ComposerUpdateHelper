# Upgrading Guide

This guide will help you upgrade Composer Update Helper to newer versions.

## General Upgrade Process

1. **Update the package**:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. **Review the CHANGELOG**:
   Check [CHANGELOG.md](CHANGELOG.md) for breaking changes and new features.

3. **Update your scripts** (if needed):
   The `generate-composer-require.sh` script is automatically updated during installation.
   If you have custom modifications, you may need to reapply them.

## Version-Specific Upgrade Notes

### Upgrading to 1.3.4+

#### New Features

- **Help option**: The script now includes a `--help` or `-h` flag to display comprehensive usage information
  - Shows all available options, examples, and framework support details
  - Works without requiring Composer to be installed

#### Performance Improvements

- **Optimized emoji handling**: Emojis are now defined as variables at script initialization
  - Reduces script size and improves execution performance
  - Better maintainability

#### Breaking Changes

- None

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. Try the new help option:
   ```bash
   ./generate-composer-require.sh --help
   ```

3. No other changes required - the script is fully backward compatible

### Upgrading to 1.3.3+

#### New Features

- **Release Information**: The script now shows GitHub release links and changelogs by default.
  - Use `--release-detail` to see full changelog details
  - Use `--no-release-info` to skip release information
  - Default mode shows summary (package, release link, changelog link)

#### Breaking Changes

- None

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. Test the new release information feature:
   ```bash
   ./generate-composer-require.sh
   ```

3. If you want to skip release information (old behavior):
   ```bash
   ./generate-composer-require.sh --no-release-info
   ```

### Upgrading to 1.3.0+

#### Breaking Changes

- PHPUnit updated to 11.0

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. Update your test suites to be compatible with PHPUnit 11.0

### Upgrading to 1.2.0+

#### Breaking Changes

- Multi-framework support added
- Framework version constraints are now automatically detected

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. The script will automatically detect your framework and respect version constraints

3. No manual configuration needed

## Troubleshooting

### Script not found after upgrade

If the script is missing after upgrading:

```bash
composer install
```

This will reinstall the script files.

### Permission errors

If you get permission errors:

```bash
chmod +x generate-composer-require.sh
```

### Conflicts with custom modifications

If you've modified the script and it conflicts with the new version:

1. Backup your custom script:
   ```bash
   cp generate-composer-require.sh generate-composer-require.sh.backup
   ```

2. Reinstall the package:
   ```bash
   composer reinstall nowo-tech/composer-update-helper
   ```

3. Reapply your custom modifications if needed

## Getting Help

If you encounter issues during upgrade:

1. Check the [CHANGELOG](CHANGELOG.md) for known issues
2. Review the [README](../README.md) for usage examples
3. Open an issue on GitHub with:
   - Your current version
   - Target version
   - Error messages
   - Steps to reproduce

