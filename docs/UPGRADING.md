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

### Upgrading to 2.0.11+

#### Changed
- **Output formatting refactored**: All output formatting is now done in PHP processor
  - Shell script is now even lighter (283 lines vs 396 lines)
  - All formatting logic centralized in PHP for better maintainability
  - Same output and functionality, just better organized

#### Migration Notes
- **No action required**: The refactoring is transparent to users
- Output format remains exactly the same
- All features work identically

#### Breaking Changes
- None

### Upgrading to 2.0.10+

#### Fixed
- **Script auto-update**: The `generate-composer-require.sh` script is now automatically updated when you run `composer update`
  - Previously, the script was only installed on first installation (`composer install`)
  - Now, the script is compared using MD5 hash and updated if content differs on both `composer install` and `composer update`
  - This ensures you always have the latest version of the script

#### Migration Notes
- **No action required**: The script will be automatically updated on your next `composer update`
- If you have custom modifications to the script, they will be overwritten
- Consider committing your customizations to a separate script or fork if needed

#### Breaking Changes
- None

### Upgrading to 2.0.9+

#### Changed
- **Refactored architecture**: The script has been split into a lightweight wrapper and a PHP processor
  - The script in your repo is now lighter (~396 lines vs ~971 lines, 59% reduction)
  - Complex logic is now in `vendor/nowo-tech/composer-update-helper/bin/process-updates.php` (~622 lines)
  - The script automatically detects and uses the PHP processor
  - No manual configuration needed

#### Migration Notes
- **No action required**: The refactoring is transparent to users
- The script automatically detects the PHP processor in vendor
- If you encounter issues, ensure `composer install` has been run
- The script will show a clear error message if the PHP processor is not found

#### Breaking Changes
- None - fully backward compatible

### Upgrading to 2.0.8+

#### Changed
- **Improved debug messages**: Debug and verbose modes now provide clearer messages when no packages are found
  - Helps identify when packages are commented out in YAML configuration
  - Makes troubleshooting configuration issues easier

#### Documentation
- **YAML configuration clarification**: README now explicitly explains that only uncommented packages are read
  - Lines starting with `#` are treated as comments and ignored
  - Examples show the difference between commented and uncommented packages

**No action required**: This is a documentation and messaging improvement. Existing configurations continue to work as before.

### Upgrading to 2.0.7+

#### Added
- **Verbose and Debug modes**: New `-v, --verbose` and `--debug` options for troubleshooting
  - Use `--verbose` or `-v` to see detailed information about configuration files and packages
  - Use `--debug` for very detailed debug information (includes verbose mode)
  - All debug/verbose output is sent to stderr to avoid interfering with normal output
- **Support for `.yml` extension**: You can now use either `.yaml` or `.yml` for configuration files
  - Priority: `.yaml` first, then `.yml`, then `.txt` (backward compatibility)
  - Allows users to use either extension based on their preference

#### Changed
- **Configuration file location**: Script now searches for configuration files in the current directory (where `composer.json` is located)
  - Previously searched in the script's directory (`$(dirname "$0")`)
  - Now searches in the current working directory (where the script is executed)
  - This ensures configuration files are found correctly regardless of script location
- **Release information default behavior**: Release information is now **disabled by default**
  - If you were relying on release information being shown automatically, you now need to use `--release-info` or `--release-detail`
  - This improves performance by default (no API calls are made)
  - Use `--release-info` to show summary with release and changelog links
  - Use `--release-detail` to show full release changelog

#### Fixed
- **Configuration file reading**: Fixed issue where script couldn't find YAML files in the project directory
  - Script now correctly searches in the current directory instead of script directory
  - Resolves issues where configuration files weren't being read properly
- **Migration verification**: Fixed issue where migration verification failed when YAML had packages in `include` section
  - Migration now correctly compares only `ignore` section with TXT content
  - TXT file is now properly deleted after successful migration

#### Migration Notes
- **No action required**: Existing configuration files will continue to work
- **File location**: If your configuration file is in the script directory, move it to the project root (where `composer.json` is)
- **Extension**: You can rename `.yaml` to `.yml` if preferred (both are supported)

#### Action Required
- **If you use release information**: Add `--release-info` or `--release-detail` flag to your commands
  - Example: `./generate-composer-require.sh --release-info`
  - Example: `./generate-composer-require.sh --run --release-detail`

### Upgrading to 2.0.6+

#### Added
- **Verification documentation**: New `docs/VERIFICATION.md` file with complete verification documentation
- **Utility scripts**: New utility scripts in `tests/` directory for development and testing

#### Changed
- **Documentation improvements**: Enhanced README.md with better release information documentation
- **YAML template**: Updated comments for clarity

**No action required**: This is a documentation and utility scripts release. No configuration changes needed.

### Upgrading to 2.0.5+

#### New Feature
- **Package inclusion**: New `include` section in `generate-composer-require.yaml`
  - You can now force include packages even if they are in the `ignore` list
  - The `include` section has priority over the `ignore` section
  - Useful for fine-grained control over which packages to update

**Example configuration:**
```yaml
ignore:
  - symfony/*  # Ignore all Symfony packages

include:
  - symfony/security-bundle  # But force include this one
```

#### Improved
- **YAML parsing**: Enhanced reading of YAML configuration files
  - Better handling of inline comments (e.g., `- package/name  # comment`)
  - Support for different indentation levels
  - More robust section detection
  - Improved handling of empty lines and comment-only lines

**No action required**: Existing configurations will continue to work. The new `include` section is optional.

### Upgrading to 2.0.4+

#### Changed
- **Migration verification**: Migration now verifies that packages were correctly migrated before deleting the old `.ignore.txt` file
  - Compares packages from TXT and YAML files to ensure data integrity
  - Only deletes TXT file if migration is verified as correct
  - Shows warning if verification fails and preserves TXT file for safety

#### Fixed
- **`.gitignore` behavior**: `.sh` and `.yaml` files are no longer added to `.gitignore`
  - These files should be committed to the repository for team collaboration
  - Plugin now removes old `.ignore.txt` entries from `.gitignore` if they exist
  - Plugin also removes `.sh` and `.yaml` entries if they were previously added (cleanup)

#### Migration Notes
- Migration is now safer with verification step
- Files should be committed to repository (not ignored)
- Old `.gitignore` entries are automatically cleaned up

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

- **Release Information**: The script can show GitHub release links and changelogs when requested.
  - Use `--release-info` to show summary (package, release link, changelog link)
  - Use `--release-detail` to see full changelog details
  - Use `--no-release-info` to explicitly skip release information (default behavior)
  - **Note**: In v2.0.7+, release information is disabled by default for better performance

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

