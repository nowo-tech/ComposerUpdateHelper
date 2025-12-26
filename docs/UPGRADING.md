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

### Upgrading to 2.0.2+

#### Fixed
- **Improved migration logic**: Migration now works even if YAML file already exists
  - Previously, migration only occurred if YAML didn't exist
  - Now migrates if YAML exists but is empty or contains only template (no user packages)
  - Prevents data loss: won't overwrite YAML files with user-defined packages
  - If you updated to v2.0.1 and migration didn't occur, updating to v2.0.2 will trigger migration

#### Migration Behavior
- **Safe migration**: The plugin detects if YAML is empty or template-only before migrating
- **Data protection**: If YAML has user-defined packages, migration is skipped to prevent data loss
- **Automatic retry**: If migration didn't happen in v2.0.1, it will work in v2.0.2

### Upgrading to 2.0.1+

#### Fixed
- **Automatic cleanup**: The old `.ignore.txt` file is now automatically deleted after successful migration to `.yaml` format
  - No manual cleanup required
  - Ensures clean migration without leftover files

### Upgrading to 2.0.0+

#### Breaking Changes

- **Configuration file format changed from TXT to YAML**
  - Old format: `generate-composer-require.ignore.txt`
  - New format: `generate-composer-require.yaml`
  - This is a **breaking change** in format, but migration is automatic

#### New Features

- **YAML configuration format**: More structured and extensible configuration file
- **Automatic migration**: Existing `.ignore.txt` files are automatically migrated to `.yaml` format
- **Backward compatibility**: The script still supports reading old TXT format if YAML doesn't exist

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. **Automatic migration**: If you have an existing `generate-composer-require.ignore.txt` file, it will be automatically migrated to `generate-composer-require.yaml` during the update.
   - **v2.0.2+**: Migration works even if YAML already exists (if YAML is empty or template-only)
   - Migration is skipped if YAML has user-defined packages (to prevent data loss)

3. Verify the migration:
   ```bash
   # Check that the new YAML file exists
   cat generate-composer-require.yaml
   ```

4. **Automatic cleanup**: The old `.ignore.txt` file is automatically deleted after successful migration (in v2.0.1+)

5. The script will now use the YAML file automatically. No changes needed to your workflow.

#### Configuration Format

**Old format (TXT)**:
```txt
# Packages to ignore
doctrine/orm
symfony/security-bundle
```

**New format (YAML)**:
```yaml
# Composer Update Helper Configuration
ignore:
  - doctrine/orm
  - symfony/security-bundle
```

#### What Happens During Migration

- Your existing ignore list is preserved
- The new `.yaml` file is created with your packages migrated (or updated if empty/template-only)
- The old `.ignore.txt` file is automatically deleted after successful migration (v2.0.1+)
- The script automatically uses the YAML file if it exists
- `.gitignore` is updated to include the new YAML file and remove the old TXT entry
- **v2.0.2+**: Migration works even if YAML exists, but only if YAML is empty or template-only (protects user data)

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

